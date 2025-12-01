<?php

namespace Database\Factories;

use App\Models\Ride; 
use App\Models\Vehiculo; 
use Illuminate\Database\Eloquent\Factories\Factory;

class RideFactory extends Factory
{
    protected $model = Ride::class;

    public function definition(): array
    {
        // Crea un vehículo y un conductor asociado
        $vehicle = Vehiculo::factory()->create(); 
        $driver = $vehicle->chofer; 

        $seats = fake()->numberBetween(1, $vehicle->capacidad); 

        return [
            'user_id' => $driver->id, 
            'vehiculo_id' => $vehicle->id, 
            
            // 🔑 CLAVE: Usar nombres de columna de la tabla 'rides'
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