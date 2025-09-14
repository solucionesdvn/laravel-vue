<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Muestra la lista de proveedores.
     */
    public function index(Request $request)
    {
        // The ForCompany trait automatically filters by the user's company.
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('contact_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Muestra el formulario de creación de proveedor.
     */
    public function create()
    {
        return Inertia::render('Suppliers/Create');
    }

    /**
     * Almacena un proveedor nuevo en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        // The ForCompany trait automatically adds the company_id.
        Supplier::create($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Muestra la información del proveedor (puedes cambiar esto si necesitas ver detalles).
     */
    public function show(Supplier $supplier)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Supplier $supplier)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Actualiza un proveedor existente.
     */
    public function update(Request $request, Supplier $supplier)
    {
        // The ForCompany trait's global scope handles authorization.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $supplier->update($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Elimina un proveedor.
     */
    public function destroy(Supplier $supplier)
    {
        // The ForCompany trait's global scope handles authorization.
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado correctamente.');
    }
}
