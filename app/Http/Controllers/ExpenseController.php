<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // The ForCompany trait automatically filters by the user's company.
        $expenses = Expense::with(['user', 'cashRegister'])
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Note: It's recommended to apply the ForCompany trait to CashRegister as well.
        $cashRegisters = CashRegister::where('company_id', auth()->user()->company_id)
            ->whereNull('closed_at')
            ->get(['id', 'opened_at']);

        return Inertia::render('Expenses/Create', [
            'cashRegisters' => $cashRegisters,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Note: It's recommended to apply the ForCompany trait to CashRegister as well.
        $cashRegister = CashRegister::where('id', $request->cash_register_id)
            ->where('company_id', auth()->user()->company_id)
            ->whereNull('closed_at')
            ->firstOrFail();

        // The ForCompany trait on Expense model automatically sets company_id.
        $expense = Expense::create([
            'cash_register_id' => $cashRegister->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'description' => $request->description,
            'notes' => $request->notes,
            'date' => now(),
        ]);

        $cashRegister->increment('total_expenses', $expense->amount);

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        // The ForCompany trait's global scope handles authorization.
        $expense->load(['user', 'cashRegister']);

        return Inertia::render('Expenses/Show', [
            'expense' => $expense,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        // The ForCompany trait's global scope handles authorization for $expense.
        
        // Note: It's recommended to apply the ForCompany trait to CashRegister as well.
        $cashRegisters = CashRegister::where('company_id', auth()->user()->company_id)
            ->whereNull('closed_at')
            ->orWhere('id', $expense->cash_register_id) // Include the current cash register even if closed
            ->get(['id', 'opened_at']);

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'cashRegisters' => $cashRegisters,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // The ForCompany trait's global scope handles authorization.
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Revert old amount from cash register
        $expense->cashRegister->decrement('total_expenses', $expense->amount);

        $expense->update($request->only(['cash_register_id', 'amount', 'description', 'notes']));

        // Add new amount to the correct cash register
        $expense->cashRegister()->find($request->cash_register_id)->increment('total_expenses', $request->amount);

        return redirect()->route('expenses.index')->with('success', 'Gasto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // The ForCompany trait's global scope handles authorization.

        // Revert amount from cash register before deleting
        $expense->cashRegister->decrement('total_expenses', $expense->amount);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado correctamente.');
    }
}
