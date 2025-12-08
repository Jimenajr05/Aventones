<?php

namespace Database\Factories;

use App\Models\Ride;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

// Importa el modelo Ride si es necesario
class RideFactory extends Factory
{
    protected $model = Ride::class;

    public function definition(): array
    {
        // Crear un chofer real
        $driver = User::factory()->create([
            'role_id' => 3 // Rol de chofer (ajústalo si tu proyecto usa otro)
        ]);

        // Crear vehículo para el chofer
        $vehicle = Vehiculo::factory()->create([
            'user_id' => $driver->id
        ]);

        // Espacios disponibles (no mayor a la capacidad del vehículo)
        $seats = fake()->numberBetween(1, $vehicle->capacidad);

        return [
            'user_id' => $driver->id,
            'vehiculo_id' => $vehicle->id,

            'nombre' => fake()->randomElement(['Ida', 'Vuelta', 'Mañana']),
            'origen' => fake()->city(),
            'destino' => fake()->city(),

            'fecha' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'hora' => fake()->time('H:i:s'),

            'costo_por_espacio' => fake()->numberBetween(1000, 5000),
            'espacios' => $seats,
        ];
    }
}
