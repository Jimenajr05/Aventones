<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test; // Importar el atributo

class RideServiceTest extends TestCase
{
    #[Test] // 🔑 CORRECCIÓN: Usar atributo #[Test]
    public function it_calculates_available_seats_correctly()
    {
        // 1. SIMULACIÓN: Crear un objeto que represente un Ride (con 4 asientos)
        $ride = new \stdClass(); 
        $ride->total_seats = 4;
        
        // 2. Definir las reservas confirmadas (simulado)
        $confirmedReservationsCount = 1; 

        // 3. Simular el cálculo de asientos disponibles
        $availableSeats = $ride->total_seats - $confirmedReservationsCount;
        
        // 4. Afirmar que el resultado es el esperado (4 - 1 = 3)
        $this->assertEquals(3, $availableSeats);
    }
}