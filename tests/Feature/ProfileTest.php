<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    #[Test]
    public function profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                // 🔑 CLAVE: Usar 'nombre' y 'apellido' y otros campos requeridos para la validación del controlador
                'nombre' => 'Test', 
                'apellido' => 'User',
                'email' => 'test@example.com',
                
                // Asegurar que se envían los campos que no cambian (si son requeridos en el ProfileUpdateRequest)
                'cedula' => $user->cedula,
                'fecha_nacimiento' => $user->fecha_nacimiento,
                
                // Campo actualizado
                'telefono' => '88887777', 
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        // 🎯 Aserciones para verificar la actualización
        $this->assertSame('Test', $user->nombre);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('88887777', $user->telefono); 
    }
    
    #[Test]
    public function user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }
}