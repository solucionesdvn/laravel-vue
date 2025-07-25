<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'contact_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'nit' => $this->faker->numerify('##########'),
            'notes' => $this->faker->sentence,
            'company_id' => Company::inRandomOrder()->first()?->id ?? 1, // usa 1 si no hay
        ];
    }
}
