<?php

namespace Tests\Feature;

use Tests\TestCase;

// Ejemplo de prueba básica para verificar que la aplicación responde correctamente
class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
