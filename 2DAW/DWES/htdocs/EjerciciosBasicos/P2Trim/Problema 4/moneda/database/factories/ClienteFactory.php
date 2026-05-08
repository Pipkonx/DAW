<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'cif' => fake()->unique()->bothify('?########'),
            'currency' => fake()->randomElement(['USD', 'GBP', 'MXN', 'JPY', 'CAD']),
        ];
    }
}
