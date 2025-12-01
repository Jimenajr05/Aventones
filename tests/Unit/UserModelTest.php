<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
// 🔥 Importar el atributo Test
use PHPUnit\Framework\Attributes\Test;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // 🔥 Reemplazamos /** @test */ por #[Test]
    #[Test] 
    public function it_can_create_a_user_with_custom_fields()
    {
        // 1. Crear un usuario usando los campos personalizados del factory corregido
        $user = User::factory()->create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan.perez@test.com',
            'cedula' => '123456789',
        ]);

        // 2. Afirmar que los datos están en la base de datos
        $this->assertDatabaseHas('users', [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan.perez@test.com',
        ]);
    }
    
    // 🔥 Reemplazamos /** @test */ por #[Test]
    #[Test]
    public function email_must_be_unique_when_creating_user()
    {
        // 1. Crear un primer usuario
        User::factory()->create(['email' => 'unique@test.com']);

        // 2. Intenta crear un segundo usuario con el mismo email
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('unique');

        User::factory()->create(['email' => 'unique@test.com']);
    }
}