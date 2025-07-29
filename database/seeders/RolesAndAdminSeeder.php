<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear o buscar empresa
        $company = Company::firstOrCreate(
            ['name' => 'Empresa de Prueba'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // 2. Crear rol admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 3. Obtener todos los permisos
        $permissions = Permission::all();

        // 4. Asignar todos los permisos al rol admin
        $adminRole->syncPermissions($permissions);

        // 5. Crear usuario administrador y asociarlo a la empresa
        $adminUser = User::firstOrCreate(
            ['email' => 'duvanrobayo4@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
                'company_id' => $company->id, // ✅ Relación con empresa
            ]
        );

        // 6. Asignar rol al usuario
        $adminUser->assignRole($adminRole);
    }
}