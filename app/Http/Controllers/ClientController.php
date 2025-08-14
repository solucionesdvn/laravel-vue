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
        $user = auth()->user();
        $query = Client::where('company_id', $user->company_id);

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
        $companyId = auth()->user()->company_id;

        $request->validate([
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

        Client::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'identification' => $request->identification,
        ]);

        return Redirect::route('clients.index')->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Client $client)
    {
        $this->authorizeCompany($client);

        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Actualiza un cliente existente.
     */
    public function update(Request $request, Client $client)
    {
        $this->authorizeCompany($client);
        $companyId = auth()->user()->company_id;

        $request->validate([
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

        $client->update($request->only(['name', 'email', 'phone', 'address', 'identification']));

        return Redirect::route('clients.index')->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Elimina un cliente.
     */
    public function destroy(Client $client)
    {
        $this->authorizeCompany($client);
        $client->delete();

        return Redirect::route('clients.index')->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * Verifica si el cliente pertenece a la empresa del usuario.
     */
    protected function authorizeCompany(Client $client)
    {
        if ($client->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}