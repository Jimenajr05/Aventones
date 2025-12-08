<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Ejecuta los seeders para poblar la base de datos con datos iniciales
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            UserStatusSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
