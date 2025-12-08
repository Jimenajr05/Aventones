<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migración para crear la tabla de roles
return new class extends Migration
{
    // Ejecutar las migraciones
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    // Revertir las migraciones
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
