<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migración para crear la tabla de reservas
return new class extends Migration
{
    // Ejecutar las migraciones
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ride_id');
            $table->unsignedBigInteger('pasajero_id'); 

            // Estado de la reserva: 1 = Activa, 0 = Cancelada
            $table->tinyInteger('estado')->default(1); 

            $table->timestamps();

            $table->foreign('ride_id')->references('id')->on('rides')->onDelete('cascade');
            $table->foreign('pasajero_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    // Revertir las migraciones
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
