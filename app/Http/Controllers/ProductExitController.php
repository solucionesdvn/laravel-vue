<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductExit;
use App\Models\ProductExitItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductExitController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = ProductExit::with('user')
            ->where('company_id', $companyId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reason', 'like', "%{$request->search}%")
                    ->orWhere('notes', 'like', "%{$request->search}%");
            });
        }

        $productExits = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('ProductExits/Index', [
            'productExits' => $productExits,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;

        return Inertia::render('ProductExits/Create', [
            'products' => Product::where('company_id', $companyId)
                ->get(['id', 'name', 'stock', 'price']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason'  => 'required|string|max:255',
            'notes'   => 'nullable|string',
            'items'   => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $companyId = auth()->user()->company_id;
            $userId = auth()->id();

            $exit = ProductExit::create([
                'company_id' => $companyId,
                'user_id'    => $userId,
                'date'       => now(),
                'reason'     => $request->reason,
                'notes'      => $request->notes,
                'total'      => 0, // Placeholder, will be updated after loop
            ]);

            $totalValue = 0;

            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto {$product->name}.");
                }

                $subtotal = $item['quantity'] * $product->price;

                ProductExitItem::create([
                    'product_exit_id' => $exit->id,
                    'product_id'      => $product->id,
                    'quantity'        => $item['quantity'],
                    'unit_price'      => $product->price,
                    'subtotal'        => $subtotal,
                ]);

                $totalValue += $subtotal;

                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Update the exit with the correct total
            $exit->total = $totalValue;
            $exit->save();

            DB::commit();

            return redirect()->route('product-exits.index')->with('success', 'Salida registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al registrar la salida: ' . $e->getMessage()]);
        }
    }

    public function show(ProductExit $productExit)
    {
        if ($productExit->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $productExit->load(['user', 'items.product']);

        return Inertia::render('ProductExits/Show', [
            'productExit' => $productExit,
        ]);
    }
}