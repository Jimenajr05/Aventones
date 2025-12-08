<?php

namespace Tests\Feature; 

use App\Models\User;
use App\Models\Ride; 
use App\Models\Reserva; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

// Test para el controlador de reservas
class BookingControllerTest extends TestCase
{
    use RefreshDatabase; 

    #[Test]
    public function store_booking_returns_success_and_creates_reservation(): void
    {
        $user = User::factory()->create(['role_id' => 4]);
        $ride = Ride::factory()->create(['espacios' => 3]); 

        $data = [
            'ride_id' => $ride->id, 
        ];

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->withoutExceptionHandling() 
            ->post('/reservas', $data); 

        $response->assertStatus(201) 
                 ->assertJson(['message' => 'Reserva creada con éxito']);
                 
        $this->assertDatabaseHas('reservas', [
            'pasajero_id' => $user->id,
            'ride_id' => $ride->id,
            'estado' => 1 
        ]);
    }
    
    #[Test]
    public function store_booking_returns_error_when_duplicate_reservation_exists(): void
    {
        $user = User::factory()->create(['role_id' => 4]);
        $ride = Ride::factory()->create(['espacios' => 3]); 
        
        Reserva::create([
            'pasajero_id' => $user->id,
            'ride_id' => $ride->id,
            'estado' => 1, 
        ]);
        
        $data = [
            'ride_id' => $ride->id,
        ];

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->withoutExceptionHandling()
            ->post('/reservas', $data);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'No se pudo crear la reserva. Verifique la disponibilidad u otros requisitos.']);
    }
    
    #[Test]
    public function store_booking_returns_validation_error_for_missing_fields(): void
    {
        $user = User::factory()->create(['role_id' => 4]);
        
        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/reservas', []); 
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['ride_id']); 
    }
}
