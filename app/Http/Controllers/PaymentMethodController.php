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
        $companyId = auth()->user()->company_id;

        $query = PaymentMethod::where('company_id', $companyId);

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
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods')->where('company_id', $companyId)
            ],
        ]);

        PaymentMethod::create($validated + ['company_id' => $companyId]);

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago creado.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $this->authorizeCompany($paymentMethod);

        return Inertia::render('PaymentMethods/Edit', [
            'paymentMethod' => $paymentMethod,
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $this->authorizeCompany($paymentMethod);
        $companyId = auth()->user()->company_id;

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
        $this->authorizeCompany($paymentMethod);
        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago eliminado.');
    }

    protected function authorizeCompany(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}