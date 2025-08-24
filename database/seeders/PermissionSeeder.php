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
            "products.export",

            

            "entries.view",
            "entries.create",
            "entries.edit",
            "entries.delete",

            "product-exits.view",
            "product-exits.create",
            "product-exits.edit",
            "product-exits.delete",

            "cash-registers.view",
            "cash-registers.create",
            "cash-registers.edit",
            "cash-registers.delete",
            "cash-registers.close",            
            
            "sales.view",
            "sales.create",
            "sales.edit",
            "sales.delete",
            
            "clients.view",
            "clients.create",
            "clients.edit",
            "clients.delete",

            "payment-methods.view",
            "payment-methods.create",
            "payment-methods.edit",
            "payment-methods.delete",

            //Permisos formatos 
            "resignation-forms.view",
            "resignation-forms.create",
            "resignation-forms.edit",
            "resignation-forms.delete",

            // Permisos Formularios Dinámicos
            "form-definitions.view",
            "form-definitions.create",
            "form-definitions.edit",
            "form-definitions.delete",

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
