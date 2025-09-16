<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

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
                \Illuminate\Validation\Rule::unique('products')
                    ->where('company_id', auth()->user()->company_id)
                    ->whereNull('deleted_at')
            ],
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|integer',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
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
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('products')
                    ->where('company_id', auth()->user()->company_id)
                    ->ignore($product->id)
                    ->whereNull('deleted_at')
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

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    public function export(Request $request)
    {
        $search = $request->query('search');

        return Excel::download(new ProductsExport($search), 'productos.xlsx');
    }

    public function search(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2',
        ]);

        $term = $request->input('term');

        $products = Product::where('stock', '>', 0)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('sku', 'like', "%{$term}%");
            })
            ->select('id', 'name', 'sku', 'price', 'stock')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function getProductsByCategory(\App\Models\Category $category)
    {
        if ($category->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $products = Product::where('category_id', $category->id)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->select('id', 'name', 'sku', 'price', 'stock')
            ->get();

        return response()->json($products);
    }

    public function kardex(Request $request, Product $product)
    {
        $perPage = 10;
        $page = $request->input('page', 1);

        // 1. Define the base UNION query for all transaction types
        $entries = \Illuminate\Support\Facades\DB::table('entry_items')
            ->join('entries', 'entry_items.entry_id', '=', 'entries.id')
            ->where('entry_items.product_id', $product->id)
            ->where('entries.company_id', $product->company_id)
            ->select('entries.date as date', 'entries.id as reference_id', \Illuminate\Support\Facades\DB::raw("'Entrada' as type"), 'entry_items.quantity as quantity_in', \Illuminate\Support\Facades\DB::raw('NULL as quantity_out'));

        $exits = \Illuminate\Support\Facades\DB::table('product_exit_items')
            ->join('product_exits', 'product_exit_items.product_exit_id', '=', 'product_exits.id')
            ->where('product_exit_items.product_id', $product->id)
            ->where('product_exits.company_id', $product->company_id)
            ->select('product_exits.date as date', 'product_exits.id as reference_id', \Illuminate\Support\Facades\DB::raw("'Salida' as type"), \Illuminate\Support\Facades\DB::raw('NULL as quantity_in'), 'product_exit_items.quantity as quantity_out');

        $sales = \Illuminate\Support\Facades\DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sale_items.product_id', $product->id)
            ->where('sales.company_id', $product->company_id)
            ->select('sales.date as date', 'sales.id as reference_id', \Illuminate\Support\Facades\DB::raw("'Venta' as type"), \Illuminate\Support\Facades\DB::raw('NULL as quantity_in'), 'sale_items.quantity as quantity_out');

        // 2. Create a query object from the UNION
        $unionQuery = $entries->union($exits)->union($sales);
        $transactionsQuery = \Illuminate\Support\Facades\DB::query()->fromSub($unionQuery, 'transactions');

        // 3. Calculate the true initial stock (before any recorded movements)
        $allMovements = (clone $transactionsQuery)->get();
        $totalIn = $allMovements->sum('quantity_in');
        $totalOut = $allMovements->sum('quantity_out');
        $initialStock = $product->stock - ($totalIn - $totalOut);

        // 4. Calculate the starting balance for the current page
        $itemsToSkip = ($page - 1) * $perPage;
        $previousMovementsQuery = (clone $transactionsQuery)
            ->orderBy('date', 'asc')
            ->orderBy('reference_id', 'asc')
            ->limit($itemsToSkip);

        $netChangeOnPreviousPages = \Illuminate\Support\Facades\DB::query()
            ->fromSub($previousMovementsQuery, 'prev')
            ->selectRaw('SUM(quantity_in) - SUM(quantity_out) as net_change')
            ->value('net_change');

        $balance = $initialStock + $netChangeOnPreviousPages;

        // 5. Get the paginated items for the current page, ordered ASC for calculation
        $paginatedTransactions = (clone $transactionsQuery)
            ->orderBy('date', 'asc')
            ->orderBy('reference_id', 'asc')
            ->paginate($perPage);

        // 6. Add the running balance to each item on the current page
        $paginatedTransactions->getCollection()->transform(function ($tx) use (&$balance) {
            $balance += ($tx->quantity_in ?? 0) - ($tx->quantity_out ?? 0);
            $tx->balance = $balance;
            return $tx;
        });

        // 7. Reverse the collection for display (newest first)
        $paginatedTransactions->setCollection($paginatedTransactions->getCollection()->reverse());

        // 8. Send the data to the view
        return Inertia::render('Products/Kardex', [
            'product' => $product,
            'transactions' => $paginatedTransactions,
            'initialStock' => $initialStock,
        ]);
    }
}
