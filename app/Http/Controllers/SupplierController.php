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
        $user = auth()->user();

        $query = Supplier::where('company_id', $user->company_id);

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
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        Supplier::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'address' => $request->address,
            'nit' => $request->nit,
            'notes' => $request->notes,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Muestra la información del proveedor (puedes cambiar esto si necesitas ver detalles).
     */
    public function show(Supplier $supplier)
    {
        // Asegura que el proveedor pertenezca a la misma empresa del usuario
        $this->authorizeCompany($supplier);

        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Supplier $supplier)
    {
        $this->authorizeCompany($supplier);

        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Actualiza un proveedor existente.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizeCompany($supplier);

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $supplier->update($request->only([
            'name', 'contact_name', 'email', 'address', 'nit', 'notes'
        ]));

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Elimina un proveedor.
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorizeCompany($supplier);

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado correctamente.');
    }

    /**
     * Verifica si el proveedor pertenece a la empresa del usuario.
     */
    protected function authorizeCompany(Supplier $supplier)
    {
        if ($supplier->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}