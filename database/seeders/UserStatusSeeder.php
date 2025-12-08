<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// Seeder para poblar la tabla user_status con estados predeterminados
class UserStatusSeeder extends Seeder
{
    // Ejecutar el seeder para poblar la tabla user_status
    public function run(): void
    {
        //Estados de usuario
        DB::table('user_status')->insert([
            ['name' => 'Pendiente'],
            ['name' => 'Activo'],
            ['name' => 'Inactivo'],
        ]);
    }
}
