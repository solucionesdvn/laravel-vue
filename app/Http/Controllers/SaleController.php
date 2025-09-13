<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\CashRegister;
use App\Models\Client;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Carbon\Carbon;

class SaleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = \App\Models\Sale::where('company_id', $companyId)
            ->with(['client', 'user'])
            ->withTrashed()
            ->latest();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $sales = $query->paginate(10)->withQueryString();

        return inertia('Sales/Index', [
            'sales' => $sales,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    public function show(\App\Models\Sale $sale)
    {
        if ($sale->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $sale->load(['client', 'user', 'paymentMethod', 'items.product']);

        return inertia('Sales/Show', [
            'sale' => $sale,
        ]);
    }

    public function create()
    {
        $this->authorize('sales.create');

        $user = auth()->user();
        $companyId = $user->company_id;
        $company = $user->company;

        // Verificar si hay una caja registradora abierta
        $cashRegister = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->latest('created_at')
            ->first();

        // Si no hay caja abierta, redirigir a la página de abrir caja con un error.
        if (!$cashRegister) {
            return redirect()->route('cash-registers.create')->withErrors(['error' => 'Primero debe abrir una caja para poder registrar ventas.']);
        }

        // Load all categories for the company. Products will be loaded via search.
        $categories = Category::where('company_id', $companyId)->get(['id', 'name', 'color']);
        
        $clients = Client::where('company_id', $companyId)->get(['id', 'name']);
        $paymentMethods = PaymentMethod::where('company_id', $companyId)->get(['id', 'name']);

        return Inertia::render('Sales/Create', [
            'categories' => $categories,
            'clients' => $clients,
            'paymentMethods' => $paymentMethods,
            'company' => $company,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'          => 'nullable|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method_id'  => 'nullable|exists:payment_methods,id',
        ]);

        $user = auth()->user();
        $companyId = $user->company_id;

        $cashRegister = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->latest('created_at')
            ->first();

        if (!$cashRegister) {
            return back()->withErrors(['error' => 'No hay una caja abierta para registrar la venta.']);
        }

        // --- NEW: Normalize items to prevent duplicate product ID submissions ---
        $normalizedItems = [];
        foreach ($request->items as $item) {
            $productId = $item['product_id'];
            if (isset($normalizedItems[$productId])) {
                $normalizedItems[$productId]['quantity'] += $item['quantity'];
            } else {
                $normalizedItems[$productId] = $item;
            }
        }
        // ---

        DB::beginTransaction();

        try {
            $realTotal = 0;
            $saleItemsData = [];

            // Process the normalized items array
            foreach ($normalizedItems as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('company_id', $companyId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                }

                $authoritativePrice = $product->price;
                $subtotal = $item['quantity'] * $authoritativePrice;
                $realTotal += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $authoritativePrice,
                    'subtotal'   => $subtotal,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $sale = Sale::create([
                'company_id'       => $companyId,
                'cash_register_id' => $cashRegister->id,
                'user_id'          => $user->id,
                'client_id'        => $request->client_id,
                'total'            => $realTotal,
                'date'             => now(),
                'payment_method_id'   => $request->payment_method_id,
            ]);

            $sale->items()->createMany($saleItemsData);

            $cashRegister->increment('total_sales', $realTotal);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Venta registrada correctamente.')->with('new_sale_id', $sale->id);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Error al registrar la venta: ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy(\App\Models\Sale $sale)
    {
        // Security check: Ensure the sale belongs to the authenticated user's company
        if ($sale->company_id !== auth()->user()->company_id) {
            abort(403); // Forbidden
        }

        // Elegant business rule check
        if (!$sale->isAnnullable()) {
            return back()->withErrors([
                'error' => 'No se puede anular una venta que pertenece a una caja ya cerrada.',
            ]);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // 1. Return stock for each item in the sale
            foreach ($sale->items as $item) {
                $product = Product::where('id', $item->product_id)
                    ->where('company_id', $sale->company_id)
                    ->lockForUpdate() // Lock product row to prevent race conditions
                    ->firstOrFail();

                $product->increment('stock', $item->quantity);
            }

            // 2. Decrement total_sales from the cash register
            $cashRegister = CashRegister::where('id', $sale->cash_register_id)
                ->where('company_id', $sale->company_id)
                ->lockForUpdate()
                ->firstOrFail();

            $cashRegister->decrement('total_sales', $sale->total);

            // 3. Soft delete the sale
            $sale->delete();

            // Commit the transaction
            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Venta anulada correctamente.');

        } catch (\Throwable $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Error al anular la venta: ' . $e->getMessage(),
            ]);
        }
    }

    public function getProductsSoldReport(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $productsSold = SaleItem::whereHas('sale', function ($query) use ($companyId, $startDate, $endDate) {
            $query->where('company_id', $companyId)
                  ->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('product:id,name,sku') // Eager load product details
        ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('product_id')
        ->orderBy('total_quantity', 'desc')
        ->get();

        return response()->json($productsSold);
    }

    public function printReceipt(Sale $sale)
    {
        // Security check
        if ($sale->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Eager load all necessary relationships
        $sale->load(['client', 'user', 'paymentMethod', 'items.product', 'company']);

        return view('sales.receipt', ['sale' => $sale]);
    }
}
