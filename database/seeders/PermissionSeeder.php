<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //php artisan db:seed --class=PermissionSeeder
        $permissions = [
            "users.view",
            "users.create",
            "users.edit",
            "users.delete",

            "roles.view",
            "roles.create",
            "roles.edit",
            "roles.delete",

            "employee.view",
            "employee.create",
            "employee.edit",
            "employee.delete",

            "companies.view",
            "companies.create",
            "companies.edit",
            "companies.delete",

            "fresignations.view",
            "fresignations.create",
            "fresignations.edit",
            "fresignations.delete",

            //Permios roles Inventario Ventas

            "categories.view",
            "categories.create",
            "categories.edit",
            "categories.delete",

            "suppliers.view",
            "suppliers.create",
            "suppliers.edit",
            "suppliers.delete",

            "products.view",
            "products.create",
            "products.edit",
            "products.delete",
            "products.updateImage",

            "entries.view",
            "entries.create",
            "entries.edit",
            "entries.delete",

            "product-exits.view",
            "product-exits.create",
            "product-exits.edit",
            "product-exits.delete"

        ];
        //foreach ($permissions as $key => $value) {
            //Permission::create(["name" => $value]);
        //}

        foreach ($permissions as $permission) {
        Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'web'
        ]);
    }
    }
}
