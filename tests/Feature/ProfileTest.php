<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

// Test para las funcionalidades del perfil de usuario
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
                'nombre' => 'Test', 
                'apellido' => 'User',
                'email' => 'test@example.com',
                'cedula' => $user->cedula,
                'fecha_nacimiento' => '2000-01-01',
                'telefono' => '88887777', 
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

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
