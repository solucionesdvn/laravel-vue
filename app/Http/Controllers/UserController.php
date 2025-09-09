<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Users/Index', [
            'filters' => $request->all('search', 'trashed'),
            'users' => User::with(['roles', 'company'])
                ->orderBy('name')
                ->filter($request->only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company' => $user->company,
                    'roles' => $user->roles,
                    'deleted_at' => $user->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render("Users/Create", [
            'companies' => Company::all(),
            "roles" => Role::pluck("name")->all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|min:6",
            "company_id" => "nullable|exists:companies,id"
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
        ]);

        $user->syncRoles($request->roles);

        return to_route("users.index")->with('success', 'Usuario creado exitosamente.');
    }

    public function show(string $id)
    {
        return Inertia::render("Users/Show", [
            "user" => User::find($id)
        ]);
    }

    public function edit(string $id)
    {
        $user = User::find($id);
        return Inertia::render("Users/Edit", [
            "user" => $user,
            "userRoles" => $user->roles()->pluck("name")->all(),
            "roles" => Role::pluck("name")->all(),
            'companies' => Company::all(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users,email," . $id,
            "company_id" => "nullable|exists:companies,id"
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->company_id = $request->company_id;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles($request->roles);

        return to_route("users.index")->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario enviado a la papelera.');
    }

    public function restore(User $user)
    {
        $user->restore();
        return back()->with('success', 'Usuario restaurado.');
    }

    public function forceDelete(User $user)
    {
        $user->forceDelete();
        return back()->with('success', 'Usuario eliminado permanentemente.');
    }
}
