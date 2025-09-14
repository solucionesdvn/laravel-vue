<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        // The ForCompany trait automatically filters by the user's company.
        $query = PaymentMethod::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $paymentMethods = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('PaymentMethods/Index', [
            'paymentMethods' => $paymentMethods,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('PaymentMethods/Create');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id; // Still needed for validation rule

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods')->where('company_id', $companyId)
            ],
        ]);

        // The ForCompany trait automatically adds the company_id.
        PaymentMethod::create($validated);

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago creado.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('PaymentMethods/Edit', [
            'paymentMethod' => $paymentMethod,
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        // The ForCompany trait's global scope handles authorization.
        $companyId = auth()->user()->company_id; // Still needed for validation rule

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods')->where('company_id', $companyId)->ignore($paymentMethod->id)
            ],
        ]);

        $paymentMethod->update($validated);

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago actualizado.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // The ForCompany trait's global scope handles authorization.
        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago eliminado.');
    }
}
