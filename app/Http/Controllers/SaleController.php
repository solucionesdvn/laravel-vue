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
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // The global scope on Sale model handles company_id filtering.
        $query = Sale::with(['client', 'user'])
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

    public function show(Sale $sale)
    {
        // Global scope on Sale model handles authorization.
        $sale->load(['client', 'user', 'paymentMethod', 'items.product']);

        return inertia('Sales/Show', [
            'sale' => $sale,
        ]);
    }

    public function create()
    {
        $this->authorize('sales.create');

        // CashRegister, Category, Client, PaymentMethod models already have the ForCompany trait.
        $cashRegister = CashRegister::whereNull('closed_at')
            ->latest('created_at')
            ->first();

        if (!$cashRegister) {
            return redirect()->route('cash-registers.create')->withErrors(['error' => 'Primero debe abrir una caja para poder registrar ventas.']);
        }

        $categories = Category::get(['id', 'name', 'color']);
        $clients = Client::get(['id', 'name']);
        $paymentMethods = PaymentMethod::get(['id', 'name']);

        return Inertia::render('Sales/Create', [
            'categories' => $categories,
            'clients' => $clients,
            'paymentMethods' => $paymentMethods,
            'company' => auth()->user()->company,
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

        // CashRegister model already has the ForCompany trait.
        $cashRegister = CashRegister::whereNull('closed_at')
            ->latest('created_at')
            ->first();

        if (!$cashRegister) {
            return back()->withErrors(['error' => 'No hay una caja abierta para registrar la venta.']);
        }

        $normalizedItems = [];
        foreach ($request->items as $item) {
            $productId = $item['product_id'];
            if (isset($normalizedItems[$productId])) {
                $normalizedItems[$productId]['quantity'] += $item['quantity'];
            } else {
                $normalizedItems[$productId] = $item;
            }
        }

        DB::beginTransaction();

        try {
            $realTotal = 0;
            $saleItemsData = [];

            foreach ($normalizedItems as $item) {
                // Product model already has the global scope.
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

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

            // The Sale model's booted method handles company_id assignment.
            $sale = Sale::create([
                'cash_register_id' => $cashRegister->id,
                'user_id'          => auth()->id(),
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

    public function destroy(Sale $sale)
    {
        // Global scope on Sale model handles authorization.
        if (!$sale->isAnnullable()) {
            return back()->withErrors([
                'error' => 'No se puede anular una venta que pertenece a una caja ya cerrada.',
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($sale->items as $item) {
                // Product model already has the global scope.
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            // CashRegister model already has the ForCompany trait.
            $cashRegister = CashRegister::lockForUpdate()->findOrFail($sale->cash_register_id);

            $cashRegister->decrement('total_sales', $sale->total);

            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Venta anulada correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Error al anular la venta: ' . $e->getMessage(),
            ]);
        }
    }

    public function getProductsSoldReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // The global scope on Sale model handles company_id filtering via the whereHas relationship.
        $productsSold = SaleItem::whereHas('sale', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('product:id,name,sku')
        ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('product_id')
        ->orderBy('total_quantity', 'desc')
        ->get();

        return response()->json($productsSold);
    }

    public function printReceipt(Sale $sale)
    {
        // Global scope on Sale model handles authorization.
        $sale->load(['client', 'user', 'paymentMethod', 'items.product', 'company']);

        return view('sales.receipt', ['sale' => $sale]);
    }
}
