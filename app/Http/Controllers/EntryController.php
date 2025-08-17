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
        $companyId = auth()->user()->company_id;

        $query = Entry::with(['supplier', 'user'])
            ->where('company_id', $companyId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
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
        $companyId = auth()->user()->company_id;

        return Inertia::render('Entries/Create', [
            'suppliers' => Supplier::where('company_id', $companyId)->get(['id', 'name']),
            'products' => Product::where('company_id', $companyId)->get(['id', 'name', 'supplier_id']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date'        => 'required|date',
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
            $companyId = auth()->user()->company_id;
            $userId = auth()->id();

            $totalCost = collect($request->items)->sum(fn ($item) =>
                $item['quantity'] * $item['purchase_price']
            );

            $entry = Entry::create([
                'company_id'  => $companyId,
                'supplier_id' => $request->supplier_id,
                'date'        => $request->date,
                'notes'       => $request->notes,
                'created_by'  => $userId,
                'total_cost'  => $totalCost,
            ]);

            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                EntryItem::create([
                    'entry_id'    => $entry->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['purchase_price'],
                    'subtotal'    => $item['quantity'] * $item['purchase_price'],
                ]);

                // Actualizar stock
                $product->stock += $item['quantity'];

                // ✅ Actualizar precio de venta si se indicó
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
        if ($entry->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $entry->load(['supplier', 'user', 'items.product']);

        return Inertia::render('Entries/Show', [
            'entry' => $entry,
        ]);
    }
}