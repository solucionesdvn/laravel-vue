<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;



class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render("Roles/Index", [
        "roles" => Role::with("permissions")->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Roles/Create", [
        "permissions" => Permission::pluck("name")->all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
        "name" => "required",
        "permissions" => "required"
        ]);

        $role = Role::create(["name" => $request->name]);

        $role->syncPermissions($request->permissions);

        return to_route("roles.index");

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        return Inertia::render("Roles/Show", [
        "role" => $role,
        "rolePermissions" => $role->permissions()->pluck("name")->all(),
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    $role = Role::find($id);
    return Inertia::render("Roles/Edit", [
        "role" => $role,
        "rolePermissions" => $role->permissions()->pluck("name")->all(),
        "permissions" => Permission::pluck("name")->all()
        
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
        "name" => "required",
        "permissions" => "required"
        ]);

        $role = Role::find($id);

        $role->name = $request->name;
        $role->save();

        $role->syncPermissions($request->permissions);

        return to_route("roles.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Role::destroy($id);

        return to_route("roles.index");
    }
}
