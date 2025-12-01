<?php

namespace Tests\Feature; 

use App\Models\User;
use App\Models\Ride; 
use App\Models\Reserva; // 🔥 Nuevo: Necesitas el modelo Reserva para verificar la creación
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BookingControllerTest extends TestCase
{
    // Usamos RefreshDatabase
    use RefreshDatabase; 

    // El test de éxito ahora comprueba la creación en la DB y el 201 JSON
    #[Test]
    public function store_booking_returns_success_and_creates_reservation(): void
    {
        // 1. Crear dependencias
        $user = User::factory()->create(['role_id' => 4]);
        $ride = Ride::factory()->create(['espacios' => 3]); 

        $data = [
            'ride_id' => $ride->id, 
        ];

        // 2. Ejecutar la petición, usando withoutExceptionHandling() para evitar 500 en caso de fallos.
        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->withoutExceptionHandling() 
            ->post('/reservas', $data); 

        // 3. Afirmar el éxito
        $response->assertStatus(201) 
                 ->assertJson(['message' => 'Reserva creada con éxito']);
                 
        // 4. Afirmar que el registro existe en la base de datos
        $this->assertDatabaseHas('reservas', [
            'pasajero_id' => $user->id,
            'ride_id' => $ride->id,
            'estado' => 1 // PENDIENTE
        ]);
    }
    
    // El test de fallo ahora comprueba el error 400 por reserva duplicada
    #[Test]
    public function store_booking_returns_error_when_duplicate_reservation_exists(): void
    {
        // 1. Crear dependencias
        $user = User::factory()->create(['role_id' => 4]);
        $ride = Ride::factory()->create(['espacios' => 3]); 
        
        // Crear la primera reserva (PENDIENTE = 1) que bloqueará la segunda
        Reserva::create([
            'pasajero_id' => $user->id,
            'ride_id' => $ride->id,
            'estado' => 1, 
        ]);
        
        $data = [
            'ride_id' => $ride->id,
        ];

        // 2. Ejecutar la petición duplicada.
        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->withoutExceptionHandling()
            ->post('/reservas', $data);

        // 3. Afirmar el fallo (400) con el mensaje de error correspondiente
        $response->assertStatus(400)
                 ->assertJson(['message' => 'No se pudo crear la reserva. Verifique la disponibilidad u otros requisitos.']);
    }
    
    #[Test]
    public function store_booking_returns_validation_error_for_missing_fields(): void
    {
        $user = User::factory()->create(['role_id' => 4]);
        
        // Ejecutar la petición sin ride_id
        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/reservas', []); 
        
        // Afirmar la validación 422. ReservaController solo valida 'ride_id'.
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['ride_id']); 
    }
}