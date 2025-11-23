<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //Funcionalidad: Crear la tabla 'reservas' para gestionar las reservas de rides por parte de los pasajeros.
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ride_id');
            $table->unsignedBigInteger('pasajero_id'); // user_id del pasajero

            $table->tinyInteger('estado')->default(1); 
            // 1 = Pendiente, 2 = Aceptada, 3 = Rechazada, 4 = Cancelada

            $table->timestamps();

            $table->foreign('ride_id')->references('id')->on('rides')->onDelete('cascade');
            $table->foreign('pasajero_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
