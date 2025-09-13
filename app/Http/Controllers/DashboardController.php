<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Employee;
use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $today = today();

        // KPIs del día
        $salesToday = Sale::where('company_id', $companyId)->whereDate('created_at', $today)->sum('total');
        $expensesToday = Expense::where('company_id', $companyId)->whereDate('date', $today)->sum('amount');
        $transactionsToday = Sale::where('company_id', $companyId)->whereDate('created_at', $today)->count();
        $averageTicketToday = ($transactionsToday > 0) ? $salesToday / $transactionsToday : 0;

        // Últimas ventas
        $recentSales = Sale::where('company_id', $companyId)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        // Cajas abiertas
        $openCashRegisters = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->with('user')
            ->get();

        // Alerta de bajo stock (umbral de 10)
        $lowStockProducts = Product::where('company_id', $companyId)
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'sku', 'stock']);

        // Top 5 productos vendidos (últimos 30 días)
        $topSellingProducts = SaleItem::whereHas('sale', function ($query) use ($companyId) {
            $query->where('company_id', $companyId)
                  ->where('created_at', '>=', now()->subDays(30));
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('product_id')
        ->orderBy('total_quantity', 'desc')
        ->limit(5)
        ->with(['product' => function ($query) {
            $query->withTrashed()->select('id', 'name', 'sku');
        }])
        ->get();

        // Ventas por categoría (últimos 30 días)
        $salesByCategory = SaleItem::whereHas('sale', function ($query) use ($companyId) {
            $query->where('company_id', $companyId)
                  ->where('created_at', '>=', now()->subDays(30));
        })
        ->with(['product' => function ($query) {
            $query->withTrashed()->with('category');
        }])
        ->get()
        ->mapToGroups(function ($item) {
            // Handle cases where product or category might be null
            if (!$item->product || !$item->product->category) {
                return ['Sin Categoría' => $item->subtotal];
            }
            return [$item->product->category->name => $item->subtotal];
        })
        ->map(function ($items, $categoryName) {
            return [
                'category_name' => $categoryName,
                'total_sales' => $items->sum(),
            ];
        })
        ->sortByDesc('total_sales')
        ->values();

        // Ventas de los últimos 7 días
        $salesData = collect(DB::select("
            SELECT
                DATE(created_at) as date,
                SUM(total) as total_sales,
                COUNT(*) as total_transactions
            FROM
                sales
            WHERE
                company_id = ?
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 DAY)
                AND deleted_at IS NULL
            GROUP BY
                DATE(created_at)
            ORDER BY
                date ASC
        ", [$companyId]))->keyBy('date');

        $period = \Carbon\CarbonPeriod::create(now()->subDays(6), now());
        $dates = collect($period->toArray())->map(function ($date) {
            return $date->format('Y-m-d');
        });

        $weeklySales = $dates->map(function ($date) use ($salesData) {
            $sale = $salesData->get($date);
            return [
                'date' => $date,
                'total_sales' => $sale->total_sales ?? 0,
                'total_transactions' => $sale->total_transactions ?? 0,
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => [
                'salesToday' => $salesToday,
                'expensesToday' => $expensesToday,
                'transactionsToday' => $transactionsToday,
                'averageTicketToday' => $averageTicketToday,
            ],
            'recentSales' => $recentSales,
            'openCashRegisters' => $openCashRegisters,
            'lowStockProducts' => $lowStockProducts,
            'topSellingProducts' => $topSellingProducts,
            'salesByCategory' => $salesByCategory,
            'weeklySales' => $weeklySales,
        ]);
    }
}
