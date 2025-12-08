<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// Seeder para insertar roles de usuario en la tabla 'roles'
class RolesSeeder extends Seeder
{
    // Ejecuta el seeder
    public function run(): void
    {
        //Roles de usuario
        DB::table('roles')->insert([
            ['name' => 'super_admin'],
            ['name' => 'admin'],
            ['name' => 'chofer'],
            ['name' => 'pasajero'],
        ]);
    }
}
