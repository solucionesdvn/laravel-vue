<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Muestra la lista de clientes.
     */
    public function index(Request $request)
    {
        // The ForCompany trait automatically filters by the user's company.
        $query = Client::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('identification', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Muestra el formulario de creación de cliente.
     */
    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    /**
     * Almacena un cliente nuevo en la base de datos.
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id; // Still needed for validation rule

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients')->where('company_id', $companyId)
            ],
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'identification' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('clients')->where('company_id', $companyId)->whereNull('deleted_at')
            ],
        ]);

        // The ForCompany trait automatically adds the company_id.
        Client::create($validatedData);

        return Redirect::route('clients.index')->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Client $client)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Actualiza un cliente existente.
     */
    public function update(Request $request, Client $client)
    {
        // The ForCompany trait's global scope handles authorization.
        $companyId = auth()->user()->company_id; // Still needed for validation rule

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients')->where('company_id', $companyId)->ignore($client->id)
            ],
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'identification' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('clients')->where('company_id', $companyId)->ignore($client->id)->whereNull('deleted_at')
            ],
        ]);

        $client->update($validatedData);

        return Redirect::route('clients.index')->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Elimina un cliente.
     */
    public function destroy(Client $client)
    {
        // The ForCompany trait's global scope handles authorization.
        $client->delete();

        return Redirect::route('clients.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function apiStore(Request $request)
    {
        $companyId = auth()->user()->company_id; // Still needed for validation rule

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('clients')->where('company_id', $companyId)
                ],
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
                'identification' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('clients')->where('company_id', $companyId)->whereNull('deleted_at')
                ],
            ]);

            // The ForCompany trait automatically adds the company_id.
            $client = Client::create($validatedData);

            return response()->json($client, 201); // 201 Created

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Throwable $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
