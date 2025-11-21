<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
