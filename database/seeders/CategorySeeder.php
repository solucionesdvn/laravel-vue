<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todas las compañías
        $companies = Company::all();

        // Si no hay compañías, no hacer nada
        if ($companies->isEmpty()) {
            $this->command->warn('No se encontraron compañías. Se omitió la creación de categorías.');
            return;
        }

        foreach ($companies as $company) {
            // Crear 5 categorías por compañía
            for ($i = 0; $i < 5; $i++) {
                Category::create([
                    'name' => 'Categoría ' . Str::random(5),
                    'description' => 'Descripción de la categoría ' . Str::random(10),
                    'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                    'company_id' => $company->id,
                ]);
            }
        }

        $this->command->info('Categorías creadas exitosamente.');
    }
}