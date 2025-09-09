<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\EntryItem;
use App\Models\SaleItem;
use App\Models\ProductExitItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $products = Product::where('company_id', $companyId)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;

        $categories = Category::where('company_id', $companyId)->get(['id', 'name']);
        $suppliers = Supplier::where('company_id', $companyId)->get(['id', 'name']);

        return Inertia::render('Products/Create', [
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where('company_id', $companyId)->whereNull('deleted_at')
            ],
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validated['company_id'] = $companyId;

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::disk('public')->put('products', $request->file('image'));
        } else {
            $validated['image'] = null;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $companyId = auth()->user()->company_id;
        $categories = Category::where('company_id', $companyId)->get(['id', 'name']);
        $suppliers = Supplier::where('company_id', $companyId)->get(['id', 'name']);

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where('company_id', $companyId)->ignore($product->id)->whereNull('deleted_at')
            ],
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|integer',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function updateImage(Request $request, Product $product)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $path = Storage::disk('public')->put('products', $request->file('image'));
        $product->update(['image' => $path]);

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Exportar productos filtrados por company_id
     */
    public function export(Request $request)
    {
        $search = $request->query('search');

        return Excel::download(new ProductsExport($search), 'productos.xlsx');
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'term' => 'required|string|min:2',
            ]);

            if (!auth()->check()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            $term = $request->input('term');
            $companyId = auth()->user()->company_id;

            $products = Product::where('company_id', $companyId)
                ->where('stock', '>', 0)
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                          ->orWhere('sku', 'like', "%{$term}%");
                })
                ->select('id', 'name', 'sku', 'price', 'stock')
                ->limit(10)
                ->get();

            return response()->json($products);

        } catch (\Throwable $e) {
            // En un entorno de producción, esto se registraría en un log.
            // Para depuración, devolvemos el mensaje de error exacto.
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getProductsByCategory(\App\Models\Category $category)
    {
        // Ensure the category belongs to the user's company for security
        if ($category->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $products = Product::where('company_id', $category->company_id)
            ->where('category_id', $category->id)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->select('id', 'name', 'sku', 'price', 'stock')
            ->get();

        return response()->json($products);
    }

    public function kardex(Product $product)
    {
        // Optional: Add authorization if you have a policy
        // $this->authorize('view', $product);

        // Security check
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Fetch all movement items
        $entryItems = \App\Models\EntryItem::where('product_id', $product->id)->with('entry:id,date')->get();
        $saleItems = \App\Models\SaleItem::where('product_id', $product->id)->with('sale:id,date')->get();
        $exitItems = \App\Models\ProductExitItem::where('product_id', $product->id)->with('productExit:id,date,reason')->get();

        // Map entries
        $entries = $entryItems->map(function ($item) {
            return [
                'date' => $item->entry->date,
                'type' => 'Entrada',
                'quantity_in' => $item->quantity,
                'quantity_out' => 0,
                'details' => 'Compra #' . $item->entry->id,
                'url' => route('entries.show', $item->entry->id),
            ];
        });

        // Map sales
        $sales = $saleItems->map(function ($item) {
            return [
                'date' => $item->sale->date,
                'type' => 'Venta',
                'quantity_in' => 0,
                'quantity_out' => $item->quantity,
                'details' => 'Venta #' . $item->sale->id,
                'url' => route('sales.show', $item->sale->id),
            ];
        });

        // Map other exits
        $exits = $exitItems->map(function ($item) {
            return [
                'date' => $item->productExit->date,
                'type' => 'Ajuste Salida',
                'quantity_in' => 0,
                'quantity_out' => $item->quantity,
                'details' => $item->productExit->reason,
                'url' => route('product-exits.show', $item->productExit->id),
            ];
        });

        // Merge, sort, and calculate balance
        $movements = $entries->concat($sales)->concat($exits)->sortBy('date')->values();

        // Recalculate balance accurately starting from the current stock and working backwards
        $kardex = $movements->sortByDesc('date')->values();
        $currentStock = $product->stock;
        $balance = $currentStock;

        $kardex = $kardex->map(function ($movement) use (&$balance) {
            $movement['balance'] = $balance;
            $balance -= ($movement['quantity_in'] - $movement['quantity_out']);
            return $movement;
        });

        // Sort back to ascending to display chronologically
        $kardex = $kardex->sortBy('date')->values();

        return Inertia::render('Products/Kardex', [
            'product' => $product,
            'kardex' => $kardex,
        ]);
    }
}