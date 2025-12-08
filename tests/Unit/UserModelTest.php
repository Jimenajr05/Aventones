<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test] 
    public function it_can_create_a_user_with_custom_fields()
    {
        $user = User::factory()->create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan.perez@test.com',
            'cedula' => '123456789',
        ]);

        $this->assertDatabaseHas('users', [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan.perez@test.com',
        ]);
    }
    
    #[Test]
    public function email_must_be_unique_when_creating_user()
    {
        User::factory()->create(['email' => 'unique@test.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('unique');

        User::factory()->create(['email' => 'unique@test.com']);
    }
}
