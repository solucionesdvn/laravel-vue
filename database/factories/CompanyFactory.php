<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'nit' => $this->faker->numerify('##########'),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
