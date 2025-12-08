<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    #[Test]
    public function new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Test',
            'apellido' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'cedula' => '1234567890',
            'fecha_nacimiento' => '2000-01-01',
            'telefono' => '88889999',
            'role_id' => 4, 
        ]);

        $response->assertRedirect('/login');

        $this->assertGuest();
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role_id' => 4, 
            'status_id' => 1, 
        ]);
        
        $this->assertNotNull(User::where('email', 'test@example.com')->first()->activation_token);
    }
}
