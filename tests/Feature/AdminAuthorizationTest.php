<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

// Test para verificar la autorización de acceso al panel de administración
class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function non_admin_cannot_access_admin_dashboard(): void
    {
        $nonAdminUser = User::factory()->create(); 
        
        $response = $this->actingAs($nonAdminUser)->get('/admin/dashboard');

        $response->assertStatus(403); 
    }

    #[Test]
    public function admin_can_access_admin_dashboard(): void
    {
        $adminUser = User::factory()->admin()->create(); 

        $response = $this->actingAs($adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
    }
}
