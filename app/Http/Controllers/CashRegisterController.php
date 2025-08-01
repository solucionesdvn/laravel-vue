<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'initial_amount' => 'required|numeric|min:0',
        ]);

        $companyId = auth()->user()->company_id;

        // Verificar si ya hay una caja abierta para esta compañía
        $alreadyOpen = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->first();

        if ($alreadyOpen) {
            return redirect()->back()->withErrors(['error' => 'Ya existe una caja abierta.']);
        }

        CashRegister::create([
            'company_id'     => $companyId,
            'user_id'        => auth()->id(),
            'opened_at'      => now(),
            'initial_amount' => $request->initial_amount,
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

    public function close(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $register = CashRegister::where('company_id', $companyId)
            ->whereNull('closed_at')
            ->first();

        if (!$register) {
            return redirect()->back()->withErrors(['error' => 'No hay una caja abierta.']);
        }

        $register->update([
            'closed_at'    => now(),
            'final_amount' => $request->final_amount ?? $register->initial_amount,
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja cerrada correctamente.');
    }
}