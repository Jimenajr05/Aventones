<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migración para crear la tabla de vehículos
return new class extends Migration
{
    // Ejecutar las migraciones
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Chofer
            $table->string('marca');
            $table->string('modelo');
            $table->string('placa')->unique();
            $table->string('color');
            $table->integer('anio');
            $table->integer('capacidad')->default(4);
            $table->string('fotografia')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    // Revertir las migraciones
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
