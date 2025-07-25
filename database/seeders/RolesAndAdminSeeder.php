<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear rol admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Obtener todos los permisos
        $permissions = Permission::all();

        // Asignar todos los permisos al rol admin
        $adminRole->syncPermissions($permissions);

        // Crear usuario administrador
        $adminUser = User::firstOrCreate(
            ['email' => 'duvanrobayo4@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password')
            ]
        );

        // Asignar rol al usuario
        $adminUser->assignRole($adminRole);
    }
}
