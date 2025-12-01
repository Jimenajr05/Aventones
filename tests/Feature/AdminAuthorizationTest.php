<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function non_admin_cannot_access_admin_dashboard(): void
    {
        // Usuario Pasajero (role_id=4)
        $nonAdminUser = User::factory()->create(); 
        
        $response = $this->actingAs($nonAdminUser)->get('/admin/dashboard');

        // La ruta /admin/dashboard requiere role:2. Se espera 403 Forbidden.
        $response->assertStatus(403); 
    }

    #[Test]
    public function admin_can_access_admin_dashboard(): void
    {
        // Usuario Admin (role_id=2)
        $adminUser = User::factory()->admin()->create(); 

        $response = $this->actingAs($adminUser)->get('/admin/dashboard');

        // Se espera 200 OK
        $response->assertStatus(200);
    }
}