<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehiculo; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RideCreationTest extends TestCase
{
    use RefreshDatabase; 

    #[Test]
    public function a_non_driver_cannot_create_a_ride(): void
    {
        $nonDriver = User::factory()->create(['role_id' => 4]); 
        
        $response = $this->actingAs($nonDriver)->get('/rides');
        
        $response->assertStatus(403);
    }
    
    #[Test]
    public function a_driver_can_create_a_ride(): void
    {
        $driver = User::factory()->create(['role_id' => 3]); 
        
        $vehiculo = Vehiculo::factory()->create([
            'user_id' => $driver->id,
            'capacidad' => 4, 
        ]);
        
        $rideData = [
            'nombre' => 'Viaje de prueba', 
            'origen' => 'Origen Test',
            'destino' => 'Destino Test',
            'fecha' => now()->addDays(7)->format('Y-m-d'), 
            'hora' => '10:00', 
            'costo_por_espacio' => 5000, 
            'espacios' => $vehiculo->capacidad - 1, 
            'vehiculo_id' => $vehiculo->id, 
        ];

        $response = $this->actingAs($driver)->post('/rides', $rideData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/rides'); 

        $this->assertDatabaseHas('rides', [
            'nombre' => 'Viaje de prueba',
            'user_id' => $driver->id,
            'vehiculo_id' => $vehiculo->id,
        ]);
    }
}
