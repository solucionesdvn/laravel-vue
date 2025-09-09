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
        $companyId = auth()->user()->company_id;

        $expenses = Expense::where('company_id', $companyId)
            ->with(['user', 'cashRegister'])
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%"); // Assuming 'notes' field will be added later
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
        $companyId = auth()->user()->company_id;
        $cashRegisters = CashRegister::where('company_id', $companyId)
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

        $user = auth()->user();
        $companyId = $user->company_id;

        // Ensure the cash register belongs to the user's company and is open
        $cashRegister = CashRegister::where('id', $request->cash_register_id)
            ->where('company_id', $companyId)
            ->whereNull('closed_at')
            ->firstOrFail();

        $expense = Expense::create([
            'cash_register_id' => $cashRegister->id,
            'user_id' => $user->id,
            'company_id' => $companyId,
            'amount' => $request->amount,
            'description' => $request->description,
            'notes' => $request->notes,
            'date' => now(), // Automatically set date
        ]);

        // Update total_expenses on the cash register
        $cashRegister->increment('total_expenses', $expense->amount);

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $this->authorizeCompany($expense);
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
        $this->authorizeCompany($expense);
        $companyId = auth()->user()->company_id;
        $cashRegisters = CashRegister::where('company_id', $companyId)
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
        $this->authorizeCompany($expense);

        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Revert old amount from cash register
        $expense->cashRegister->decrement('total_expenses', $expense->amount);

        $expense->update([
            'cash_register_id' => $request->cash_register_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        // Add new amount to cash register
        $expense->cashRegister->increment('total_expenses', $expense->amount);

        return redirect()->route('expenses.index')->with('success', 'Gasto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorizeCompany($expense);

        // Revert amount from cash register before deleting
        $expense->cashRegister->decrement('total_expenses', $expense->amount);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado correctamente.');
    }

    /**
     * Authorize that the expense belongs to the authenticated user's company.
     */
    protected function authorizeCompany(Expense $expense)
    {
        if ($expense->company_id !== auth()->user()->company_id) {
            abort(403, 'No autorizado.');
        }
    }
}
