<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    
    protected static ?string $password;

    public function definition(): array
    {
        return [
            // CAMPOS DEL USUARIO
            'nombre' => fake()->firstName(), 
            'apellido' => fake()->lastName(),
            
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            
            // CAMPOS PERSONALIZADOS
            'cedula' => fake()->numerify('##########'), 
            'fecha_nacimiento' => fake()->date('Y-m-d', '2005-01-01'), 
            'telefono' => fake()->numerify('########'), 
            
            // VALORES POR DEFECTO
            'role_id' => 4, // Pasajero por defecto
            'status_id' => 2, // Activo por defecto
            'foto' => null, 
            'activation_token' => null,
            'is_super_admin' => false,
        ];
    }
    
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    
    // ESTADO PARA ADMINISTRADOR (role_id = 2)
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 2, // Admin
            'status_id' => 2,
        ]);
    }

    // 🔑 ESTADO PARA CONDUCTOR/CHOFER (role_id = 3)
    public function driver(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 3, // Chofer
            'status_id' => 2,
        ]);
    }
}