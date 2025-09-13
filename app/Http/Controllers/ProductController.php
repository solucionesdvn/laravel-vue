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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validated['company_id'] = $companyId;

        /*
        if ($request->hasFile('image')) {
            $validated['image'] = Storage::disk('public')->put('products', $request->file('image'));
        } else {
            $validated['image'] = null;
        }
        */
        $validated['image'] = null; // Set image to null when not uploading

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

    // public function updateImage(Request $request, Product $product)
    // {
    //     $validated = $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);

    //     if ($product->image) {
    //         Storage::disk('public')->delete($product->image);
    //     }

    //     $path = Storage::disk('public')->put('products', $request->file('image'));
    //     $product->update(['image' => $path]);

    //     return back()->with('success', 'Imagen actualizada correctamente.');
    // }

    public function destroy(Product $product)
    {
        /*
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        */
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

    public function kardex(Request $request, Product $product)
    {
        // Security check
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $perPage = 10; // Or whatever you want

        // Fetch and paginate Entry Items
        $entryKardex = \App\Models\EntryItem::where('product_id', $product->id)
            ->with('entry:id,date')
            ->latest('created_at') // Order by creation date descending
            ->paginate($perPage, ['*'], 'entryPage', $request->input('entryPage'))
            ->through(function ($item) {
                return [
                    'date' => $item->entry->date,
                    'type' => 'Entrada',
                    'details' => 'Movimiento de Entrada',
                    'quantity_in' => $item->quantity,
                    'quantity_out' => 0,
                    'url' => route('entries.show', $item->entry->id),
                ];
            });

        // Fetch and paginate Sale Items
        $saleKardex = \App\Models\SaleItem::where('product_id', $product->id)
            ->withTrashed() // Include soft-deleted SaleItems
            ->with(['sale' => function ($query) {
                $query->withTrashed()->select('id', 'date'); // Include soft-deleted Sales
            }])
            ->latest('created_at') // Order by creation date descending
            ->paginate($perPage, ['*'], 'salePage', $request->input('salePage'))
            ->through(function ($item) {
                return [
                    'date' => $item->sale->date,
                    'type' => 'Venta',
                    'details' => 'Movimiento de Venta',
                    'quantity_in' => 0,
                    'quantity_out' => $item->quantity,
                    'url' => route('sales.show', $item->sale->id),
                ];
            });

        // Fetch and paginate Product Exit Items
        $exitKardex = \App\Models\ProductExitItem::where('product_id', $product->id)
            ->with('productExit:id,date,reason')
            ->latest('created_at') // Order by creation date descending
            ->paginate($perPage, ['*'], 'exitPage', $request->input('exitPage'))
            ->through(function ($item) {
                return [
                    'date' => $item->productExit->date,
                    'type' => 'Ajuste Salida',
                    'details' => $item->productExit->reason,
                    'quantity_in' => 0,
                    'quantity_out' => $item->quantity,
                    'url' => route('product-exits.show', $item->productExit->id),
                ];
            });

        return Inertia::render('Products/Kardex', [
            'product' => $product,
            'entryKardex' => $entryKardex,
            'saleKardex' => $saleKardex,
            'exitKardex' => $exitKardex,
        ]);
    }
}