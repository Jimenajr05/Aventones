<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vehiculo; 
use App\Models\User;

class VehiculoFactory extends Factory
{
    protected $model = Vehiculo::class;

    public function definition(): array
    {
        // Crea un Chofer (role_id=3) para ser el dueño del vehículo
        $driver = User::factory()->driver()->create();

        return [
            'user_id' => $driver->id,
            'marca' => fake()->randomElement(['Toyota', 'Nissan', 'Hyundai']),
            'modelo' => fake()->safeColorName(),
            'placa' => fake()->unique()->bothify('???###'),
            'color' => fake()->colorName(),
            'anio' => fake()->year(),
            'capacidad' => fake()->numberBetween(1, 4), // Usar capacidad del modelo Vehiculo
            'fotografia' => null,
        ];
    }
}