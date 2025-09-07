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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::with(['roles', 'company'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Users/Create", [
            'companies' => Company::all(),
            "roles" => Role::pluck("name")->all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render("Users/Show", [
            "user" => User::find($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);
        return to_route("users.index")->with('success', 'Usuario eliminado exitosamente.');
    }
}