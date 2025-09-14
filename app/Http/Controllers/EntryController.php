<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\EntryItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        // The ForCompany trait automatically filters Entry queries.
        $query = Entry::with(['supplier', 'user']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                // Since Supplier model is already refactored, we can rely on its global scope.
                $q->whereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$request->search}%"))
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

    public function create()
    {
        // Supplier and Product models are already refactored and use the global scope.
        return Inertia::render('Entries/Create', [
            'suppliers' => Supplier::get(['id', 'name']),
            'products' => Product::get(['id', 'name', 'supplier_id']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.purchase_price'  => 'required|numeric|min:0',
            'items.*.update_price'    => 'nullable|boolean',
            'items.*.margin'          => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $totalCost = collect($request->items)->sum(fn ($item) =>
                $item['quantity'] * $item['purchase_price']
            );

            // The ForCompany trait on Entry model automatically sets company_id.
            $entry = Entry::create([
                'supplier_id' => $request->supplier_id,
                'date'        => now(),
                'notes'       => $request->notes,
                'created_by'  => auth()->id(),
                'total_cost'  => $totalCost,
            ]);

            foreach ($request->items as $item) {
                // Product model is already refactored and uses the global scope.
                $product = Product::findOrFail($item['product_id']);

                EntryItem::create([
                    'entry_id'    => $entry->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['purchase_price'],
                    'subtotal'    => $item['quantity'] * $item['purchase_price'],
                ]);

                $product->stock += $item['quantity'];

                if (!empty($item['update_price']) && isset($item['margin'])) {
                    $margin = floatval($item['margin']);
                    $product->price = $item['purchase_price'] * (1 + $margin / 100);
                }

                $product->save();
            }

            DB::commit();

            return redirect()->route('entries.index')->with('success', 'Entrada registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error al registrar la entrada: ' . $e->getMessage());
        }
    }

    public function show(Entry $entry)
    {
        // The ForCompany trait's global scope handles authorization.
        $entry->load(['supplier', 'user', 'items.product']);

        return Inertia::render('Entries/Show', [
            'entry' => $entry,
        ]);
    }
}
