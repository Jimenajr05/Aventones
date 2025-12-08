<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migración para agregar el token de activación a la tabla de usuarios
return new class extends Migration
{
    // Ejecutar las migraciones
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_token')->nullable()->after('status_id');
        });
    }

    // Revertir las migraciones 
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_token');
        });
    }
};
