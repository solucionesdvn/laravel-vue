<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\EntryItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Entry::with(['supplier', 'user'])
            ->where('company_id', $companyId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('supplier', fn($s) => $s->where('name', 'like', "%{$request->search}%"))
                ->orWhere('notes', 'like', "%{$request->search}%");
            });
        }

        $entries = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Entries/Index', [
            'entries' => $entries,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'update_price' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $companyId = auth()->user()->company_id;
            $userId = auth()->id();
            $totalCost = collect($request->items)->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            // 1. Crear la entrada
            $entry = Entry::create([
                'company_id' => $companyId,
                'supplier_id' => $request->supplier_id,
                'date' => $request->date,
                'notes' => $request->notes,
                'created_by' => $userId,
                'total_cost' => $totalCost,
            ]);

            // 2. Crear los items y actualizar stock / precio
            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                EntryItem::create([
                    'entry_id' => $entry->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                // Actualizar stock
                $product->stock += $item['quantity'];

                // (Opcional) actualizar precio de venta si se indicó
                if ($request->update_price) {
                    $product->price = $item['unit_price'] * 1.3; // margen 30%
                }

                $product->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Entrada registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error al registrar la entrada: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return Inertia::render('Entries/Create', [
            // Aquí podrías pasar: lista de productos, proveedores, etc.
        ]);
    }
}
