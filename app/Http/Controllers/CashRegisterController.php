<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashRegisterController extends Controller
{
    public function index()
    {
        // The ForCompany trait automatically filters CashRegister and PaymentMethod queries.
        $openRegister = CashRegister::whereNull('closed_at')
            ->with('user')
            ->with(['sales' => function ($query) {
                $query->select('cash_register_id', 'payment_method_id', \DB::raw('SUM(total) as total_sales_by_method'))
                      ->groupBy('cash_register_id', 'payment_method_id');
            }])
            ->withSum('sales', 'total')
            ->withCount('sales')
            ->withSum('expenses', 'amount')
            ->first();

        $closedRegisters = CashRegister::whereNotNull('closed_at')
            ->with('user')
            ->with(['sales' => function ($query) {
                $query->select('cash_register_id', 'payment_method_id', \DB::raw('SUM(total) as total_sales_by_method'))
                      ->groupBy('cash_register_id', 'payment_method_id');
            }])
            ->withSum('expenses', 'amount')
            ->latest()
            ->paginate(10);

        $paymentMethods = PaymentMethod::get(['id', 'name']);

        return Inertia::render('CashRegisters/Index', [
            'openRegister' => $openRegister,
            'closedRegisters' => $closedRegisters,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function create()
    {
        return Inertia::render('CashRegisters/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // The ForCompany trait automatically filters this query.
        $hasOpenRegister = CashRegister::whereNull('closed_at')->exists();

        if ($hasOpenRegister) {
            return back()->withErrors(['opening_amount' => 'Ya existe una caja abierta.'])->withInput();
        }

        // The ForCompany trait automatically adds company_id.
        CashRegister::create([
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_amount' => $request->opening_amount,
            'notes' => $request->notes,
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja abierta correctamente.');
    }

    public function show()
    {
        // The ForCompany trait automatically filters this query.
        $current = CashRegister::whereNull('closed_at')
            ->with('user')
            ->first();

        return Inertia::render('CashRegisters/Show', [
            'register' => $current,
        ]);
    }

    // Formulario para cerrar
    public function close(CashRegister $cashRegister)
    {
        // The ForCompany trait's global scope handles authorization.
        if ($cashRegister->closed_at) {
            return redirect()->route('cash-registers.index')->with('error', 'La caja ya fue cerrada.');
        }

        return Inertia::render('CashRegisters/Close', [
            'register' => $cashRegister,
        ]);
    }

    // Procesar cierre
    public function update(Request $request, CashRegister $cashRegister)
    {
        // The ForCompany trait's global scope handles authorization.
        $request->validate([
            'closing_amount' => 'nullable|numeric|min:0',
        ]);

        if ($cashRegister->closed_at) {
            return redirect()->route('cash-registers.index')->with('error', 'La caja ya fue cerrada.');
        }

        $cashRegister->update([
            'closed_at' => now(),
            'closing_amount' => $request->closing_amount ?? (
                $cashRegister->opening_amount + $cashRegister->total_sales - $cashRegister->total_expenses
            ),
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja cerrada correctamente.');
    }
}
