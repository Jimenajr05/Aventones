<?php

namespace Database\Factories;

use App\Models\Ride;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

class RideFactory extends Factory
{
    protected $model = Ride::class;

    public function definition(): array
    {
        // Lugares posibles para origen y destino (solo Alajuela)
        $lugaresAlajuela = [
            'Alajuela Centro', 'San José', 'Carrizal', 'San Antonio', 'Guácima',
            'San Isidro', 'Tambor', 'Garita', 'Sarapiquí',
            'Grecia Centro', 'Puente Piedra', 'Tacares', 'San Roque', 'San Isidro de Grecia',
            'San Ramón Centro', 'Santiago', 'Piedades Sur', 'Volio',
            'Atenas Centro', 'Sabana Larga', 'Mercedes', 'Río Grande',
            'Palmares Centro', 'Zaragoza', 'Esquipulas',
            'Aeropuerto Juan Santamaría', 'Parque Central Alajuela', 'City Mall Alajuela',
            'Desamparados de Alajuela', 'Naranjo Centro', 'Poás', 'Fraijanes'
        ];

        // Elegir origen y destino diferentes
        $origen = fake()->randomElement($lugaresAlajuela);
        $destino = fake()->randomElement(array_values(array_diff($lugaresAlajuela, [$origen])));

        return [
            // Crea un chofer
            'user_id' => User::factory()->state(['role_id' => 3]),

            // Crea un vehículo asociado
            'vehiculo_id' => Vehiculo::factory(),

            // Datos generales del ride
            'nombre' => fake()->randomElement(['Ride 1', 'Ride 2', 'Ride 3']),
            'origen' => $origen,
            'destino' => $destino,
            'fecha' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'hora' => fake()->time('H:i:s'),
            'costo_por_espacio' => fake()->numberBetween(1000, 5000),
            'espacios' => fake()->numberBetween(1, 4),
        ];
    }

    // Ajusta espacios según capacidad del vehículo
    public function configure()
    {
        return $this->afterCreating(function (Ride $ride) {
            $vehiculo = $ride->vehiculo;
            $maxPasajeros = max(1, $vehiculo->capacidad - 1);

            if ($ride->espacios > $maxPasajeros) {
                $ride->update(['espacios' => $maxPasajeros]);
            }
        });
    }
}
