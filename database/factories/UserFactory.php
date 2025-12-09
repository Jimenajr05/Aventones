<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            // Datos personales
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'email' => fake()->unique()->userName() . '@gmail.com',
            'email_verified_at' => now(),

            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),

            // Campos personalizados de la app
            'cedula' => fake()->unique()->numerify('##########'),
            'fecha_nacimiento' => fake()->dateTimeBetween('-1200 years', '-13 years')->format('Y-m-d'),
            'telefono' => fake()->unique()->numerify('########'),

            // Valores por defecto
            'role_id' => 4,   // Pasajero
            'status_id' => 2, // Activo
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
