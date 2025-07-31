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
            'date'    => 'required|date',
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

            $total = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * ($item['unit_price'] ?? 0);
            });

            $exit = ProductExit::create([
                'company_id' => $companyId,
                'user_id'    => $userId,
                'date'       => $request->date,
                'reason'     => $request->reason,
                'notes'      => $request->notes,
                'total'      => $total,
            ]);

            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto {$product->name}.");
                }

                ProductExitItem::create([
                    'product_exit_id' => $exit->id,
                    'product_id'      => $product->id,
                    'quantity'        => $item['quantity'],
                    'unit_price'      => $product->price,
                    'subtotal'        => $item['quantity'] * $product->price,
                ]);

                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            return redirect()->route('product-exits.index')->with('success', 'Salida registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al registrar la salida: ' . $e->getMessage()]);
        }
    }
}