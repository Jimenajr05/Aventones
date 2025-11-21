<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
