<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class SaleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Inertia::render('Sales/Index', [
            // Aquí luego puedes enviar historial de ventas
        ]);
    }

    public function create()
    {
        $this->authorize('sales.create');

        $companyId = auth()->user()->company_id;

        $categories = Category::whereHas('products', function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                      ->where('stock', '>', 0);
            })
            ->with(['products' => function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                      ->where('stock', '>', 0)
                      ->select('id', 'name', 'stock', 'price', 'category_id');
            }])
            ->get(['id', 'name', 'color']);

        return Inertia::render('Sales/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method'     => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $companyId = $user->company_id;

        $cashRegister = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->latest('created_at')
            ->first();

        if (!$cashRegister) {
            return back()->withErrors(['error' => 'No hay una caja abierta para registrar la venta.']);
        }

        DB::beginTransaction();

        try {
            $total = collect($request->items)->sum(fn ($item) =>
                $item['quantity'] * $item['unit_price']
            );

            $sale = Sale::create([
                'company_id'       => $companyId,
                'cash_register_id' => $cashRegister->id,
                'user_id'          => $user->id,
                'total'            => $total,
                'date'             => now(),
                'payment_method'   => $request->payment_method,
            ]);

            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('company_id', $companyId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                }

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $item['quantity'] * $item['unit_price'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            $cashRegister->increment('total_sales', $total);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Venta registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Error al registrar la venta: ' . $e->getMessage(),
            ]);
        }
    }
}