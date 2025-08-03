<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashRegisterController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $registers = CashRegister::where('company_id', $companyId)
            ->with('user')
            ->latest()
            ->get();

        return Inertia::render('CashRegisters/Index', [
            'registers' => $registers,
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

        $user = auth()->user();

        // Validar que no exista caja abierta para esta empresa
        $hasOpenRegister = CashRegister::where('company_id', $user->company_id)
            ->whereNull('closed_at')
            ->exists();

        if ($hasOpenRegister) {
            return back()->withErrors(['opening_amount' => 'Ya existe una caja abierta.'])->withInput();
        }

        CashRegister::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'opened_at' => now(),
            'opening_amount' => $request->opening_amount,
            'notes' => $request->notes,
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja abierta correctamente.');
    }

    public function show()
    {
        $companyId = auth()->user()->company_id;

        $current = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->with('user')
            ->first();

        return Inertia::render('CashRegisters/Show', [
            'register' => $current,
        ]);
    }

    // Formulario para cerrar
    public function close(CashRegister $cashRegister)
    {
        $this->authorizeCompany($cashRegister);

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
        $this->authorizeCompany($cashRegister);

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

    protected function authorizeCompany(CashRegister $cashRegister)
    {
        if ($cashRegister->company_id !== auth()->user()->company_id) {
            abort(403, 'No autorizado.');
        }
    }
}