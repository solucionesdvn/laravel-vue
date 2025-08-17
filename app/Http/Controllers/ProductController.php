<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
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
}