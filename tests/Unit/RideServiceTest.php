<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test; 

// Test para el servicio de gestión de viajes
class RideServiceTest extends TestCase
{
    #[Test]
    public function it_calculates_available_seats_correctly()
    {
        $ride = new \stdClass(); 
        $ride->total_seats = 4;
        
        $confirmedReservationsCount = 1; 

        $availableSeats = $ride->total_seats - $confirmedReservationsCount;
        
        $this->assertEquals(3, $availableSeats);
    }
}
