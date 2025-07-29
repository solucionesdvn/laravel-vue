<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        $products = Product::with(['category', 'supplier'])
            ->where('company_id', $user->company_id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'price' => $product->price,
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'category' => [
                        'name' => $product->category->name,
                    ],
                    'supplier' => [
                        'name' => $product->supplier->name,
                    ],
                ];
            })
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        return Inertia::render('Products/Create', [
            'categories' => Category::where('company_id', $user->company_id)->get(['id', 'name']),
            'suppliers' => Supplier::where('company_id', $user->company_id)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0|max:9999999.99',

            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return to_route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $this->authorizeCompany($product);

        $user = auth()->user();

        return Inertia::render('Products/Edit', [
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'supplier_id' => $product->supplier_id,
                'price' => $product->price,
                'image' => $product->image ? asset("storage/{$product->image}") : null,
            ],
            'categories' => Category::where('company_id', $user->company_id)->get(['id', 'name']),
            'suppliers' => Supplier::where('company_id', $user->company_id)->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeCompany($product);

        $validated = $request->validate([
            'sku' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return to_route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function updateImage(Request $request, Product $product)
    {
        $this->authorizeCompany($product);

        $validated = $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $path = $request->file('image')->store('products', 'public');
        $product->update(['image' => $path]);

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeCompany($product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return to_route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    protected function authorizeCompany(Product $product)
    {
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
