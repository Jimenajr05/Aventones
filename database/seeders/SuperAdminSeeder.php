<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Tabla de Super Usuario
        DB::table('users')->insert([
            'nombre' => 'Super',
            'apellido' => 'Admin',
            'cedula' => '208710340',
            'fecha_nacimiento' => '2005-10-14',
            'telefono' => '63342879',
            'foto' => null,

            'email' => 'super@admin.com',
            'password' => Hash::make('SuperAdmin1234'),

            'role_id' => 1,       // super_admin
            'status_id' => 2,     // Activo
            'is_super_admin' => true,

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
