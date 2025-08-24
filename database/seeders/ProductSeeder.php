<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Company;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first company to associate products with
        $company = Company::first();

        if (!$company) {
            $this->command->info('No company found, skipping ProductSeeder.');
            return;
        }

        // Get all categories for that company
        $categories = Category::where('company_id', $company->id)->get();

        if ($categories->isEmpty()) {
            $this->command->info('No categories found for the company, skipping ProductSeeder.');
            return;
        }

        foreach ($categories as $category) {
            for ($i = 1; $i <= 30; $i++) {
                Product::create([
                    'name' => "{$category->name} Producto {$i}",
                    'sku' => strtoupper("{$category->name[0]}{$category->name[1]}") . "-{$category->id}-{$i}",
                    'stock' => fake()->numberBetween(10, 100),
                    'price' => fake()->randomFloat(2, 1000, 50000),
                    'cost_price' => fake()->randomFloat(2, 500, 40000),
                    'category_id' => $category->id,
                    'supplier_id' => null, // As requested
                    'company_id' => $company->id,
                ]);
            }
        }
    }
}