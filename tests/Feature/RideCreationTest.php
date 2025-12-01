<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehiculo; 
use Illuminate\Foundation\Testing\RefreshDatabase;
// Se ELIMINA 'use Illuminate\Foundation\Testing\WithoutMiddleware;'
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RideCreationTest extends TestCase
{
    // Se ELIMINA 'WithoutMiddleware'
    use RefreshDatabase; 

    #[Test]
    public function a_non_driver_cannot_create_a_ride(): void
    {
        // 1. Crear un usuario Pasajero (role_id = 4)
        $nonDriver = User::factory()->create(['role_id' => 4]); 
        
        // 2. Simular el intento de acceder a una ruta protegida por 'role:3'
        // Se usa GET /rides que sí existe y está protegida.
        $response = $this->actingAs($nonDriver)->get('/rides');
        
        // 3. Afirmar el fallo de rol (403 Forbidden)
        $response->assertStatus(403);
    }
    
    #[Test]
    public function a_driver_can_create_a_ride(): void
    {
        // 1. Crear un conductor (role_id = 3)
        $driver = User::factory()->create(['role_id' => 3]); 
        
        // 2. Crear un vehículo asociado
        $vehiculo = Vehiculo::factory()->create([
            'user_id' => $driver->id,
            'capacidad' => 4, 
        ]);
        
        // 3. Datos del viaje
        $rideData = [
            'nombre' => 'Viaje de prueba', 
            'origen' => 'Origen Test',
            'destino' => 'Destino Test',
            'fecha' => now()->addDays(7)->format('Y-m-d'), 
            'hora' => '10:00', 
            'costo_por_espacio' => 5000, 
            'espacios' => $vehiculo->capacidad, 
            'vehiculo_id' => $vehiculo->id, 
        ];

        $response = $this->actingAs($driver)->post('/rides', $rideData);

        $response->assertSessionHasNoErrors();
        // 5. La redirección es a /rides (rides.index)
        $response->assertRedirect('/rides'); 

        // 🔥 CORRECCIÓN: Usar 'user_id' en lugar de 'chofer_id'
        $this->assertDatabaseHas('rides', [
            'nombre' => 'Viaje de prueba',
            'user_id' => $driver->id, // ¡CAMBIADO a user_id!
            'vehiculo_id' => $vehiculo->id,
        ]);
    }
}