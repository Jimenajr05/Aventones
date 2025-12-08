<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vehiculo; 
use App\Models\User;

// Importa el modelo Vehiculo si es necesario
class VehiculoFactory extends Factory
{
    protected $model = Vehiculo::class;

    public function definition(): array
    {
        return [
            // Crea automáticamente un chofer usando la factory
            'user_id' => User::factory()->driver(),

            'marca' => fake()->randomElement(['Toyota', 'Nissan', 'Hyundai']),
            'modelo' => fake()->word(),
            'placa' => fake()->unique()->bothify('???###'),
            'color' => fake()->colorName(),
            'anio' => fake()->year(),
            'capacidad' => fake()->numberBetween(2, 6),
            'fotografia' => null,
        ];
    }
}
