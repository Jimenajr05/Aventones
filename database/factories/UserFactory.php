<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Importa el modelo User si es necesario
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            // Datos personales
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),

            // Datos personalizados de tu app
            'cedula' => fake()->numerify('##########'),
            'fecha_nacimiento' => fake()->date(),
            'telefono' => fake()->numerify('########'),

            // Valores por defecto
            'role_id' => 4,             // Pasajero
            'status_id' => 2,           // Activo
            'foto' => null,
            'activation_token' => null,
            'is_super_admin' => false,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => 2,
            'status_id' => 2,
        ]);
    }

    public function driver(): static
    {
        return $this->state(fn () => [
            'role_id' => 3,
            'status_id' => 2,
        ]);
    }
}
