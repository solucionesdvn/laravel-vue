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
        $products = Product::with(['category', 'supplier'])
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
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                // The global scope doesn't apply to validation rules, so we keep the company_id check here.
                \Illuminate\Validation\Rule::unique('products')->where('company_id', auth()->user()->company_id)->whereNull('deleted_at')
            ],
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|integer',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // The ForCompany trait automatically sets company_id.
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        // Route Model Binding with the global scope already ensures the product belongs to the user's company.
        // If not, a 404 is automatically thrown.

        // Note: Apply the ForCompany trait to Category and Supplier models as well.
        $companyId = auth()->user()->company_id;
        $categories = Category::where('company_id', $companyId)->get(['id', 'name']);
        $suppliers = Supplier::where('company_id', $companyId)->get(['id', 'name']);

        return Inertia::render('Products/Edit', [
            'product' => $product->load(['category', 'supplier']),
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // Route Model Binding with the global scope already ensures the product belongs to the user's company.
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('products')->where('company_id', auth()->user()->company_id)->ignore($product->id)->whereNull('deleted_at')
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
        // Route Model Binding with the global scope already ensures the product belongs to the user's company.
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

            // The global scope automatically filters by company_id.
            $products = Product::where('stock', '>', 0)
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
        // Security check for the category itself.
        // It's recommended to also apply the ForCompany trait to the Category model.
        if ($category->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // The global scope on Product handles the company_id filtering automatically.
        $products = Product::where('category_id', $category->id)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->select('id', 'name', 'sku', 'price', 'stock')
            ->get();

        return response()->json($products);
    }

    public function kardex(Request $request, Product $product)
    {
        // The security check is no longer needed here due to Route Model Binding + Global Scope.
        $perPage = 10; // Or whatever you want

        // The queries for EntryItem, SaleItem, etc., should also be secured,
        // ideally by applying a similar scope or checking the company_id via relationships.
        $entryKardex = \App\Models\EntryItem::where('product_id', $product->id)
            ->with('entry:id,date')
            ->latest('created_at') // Order by creation date descending
            ->paginate($perPage, ['*'], 'entryPage', $request->input('entryPage'));

        // Fetch and paginate Sale Items
        $saleKardex = \App\Models\SaleItem::where('product_id', $product->id)
            ->withTrashed() // Include soft-deleted SaleItems
            ->with(['sale' => function ($query) {
                $query->withTrashed()->select('id', 'date'); // Include soft-deleted Sales
            }])
            ->latest('created_at') // Order by creation date descending
            ->paginate($perPage, ['*'], 'salePage', $request->input('salePage'));

        // Fetch and paginate Product Exit Items
        $exitKardex = \App\Models\ProductExitItem::where('product_id', $product->id)
            ->with('productExit:id,date,reason')
            ->latest('created_at') // Order by creation date descending
            ->paginate($perPage, ['*'], 'exitPage', $request->input('exitPage'));

        return Inertia::render('Products/Kardex', [
            'product' => $product,
            'entryKardex' => $entryKardex,
            'saleKardex' => $saleKardex,
            'exitKardex' => $exitKardex,
        ]);
    }
}