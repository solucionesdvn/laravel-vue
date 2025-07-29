<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            PermissionSeeder::class,
            RolesAndAdminSeeder::class,
            SupplierSeeder::class,
            CategorySeeder::class,
        ]);
    }
}
