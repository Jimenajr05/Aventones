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
        return [
            // Asigna chofer automáticamente
            'user_id' => User::factory()->driver(),

            'marca' => fake()->randomElement(['Toyota', 'Nissan', 'Hyundai', 'Kia', 'Honda']),
            'modelo' => fake()->randomElement(['Corolla', 'Yaris', 'Elantra', 'Civic', 'Sentra']),
            
            'placa' => fake()->unique()->bothify('???###'),

            'color' => fake()->safeColorName(),

            // Año realista y nunca futuro
            'anio' => fake()->numberBetween(1990, now()->year),

            // Capacidad mínima 2 (chofer + pasajero)
            'capacidad' => fake()->numberBetween(2, 7),

            // No generar fotos en tests
            'fotografia' => null,
        ];
    }
}
