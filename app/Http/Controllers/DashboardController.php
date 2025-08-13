<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Employee;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        // Estadísticas generales
        $totalClients = Client::where('company_id', $companyId)->count();
        $totalProducts = Product::where('company_id', $companyId)->count();
        $totalEmployees = Employee::count();
        $totalSalesAmount = Sale::where('company_id', $companyId)->sum('total');

        // Últimas ventas
        $recentSales = Sale::where('company_id', $companyId)
            ->with('client') // Cargar el cliente relacionado
            ->latest()
            ->take(5)
            ->get();

        // Cajas abiertas
        $openCashRegisters = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->with('user')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalClients' => $totalClients,
                'totalProducts' => $totalProducts,
                'totalEmployees' => $totalEmployees,
                'totalSalesAmount' => $totalSalesAmount,
            ],
            'recentSales' => $recentSales,
            'openCashRegisters' => $openCashRegisters,
        ]);
    }
}
