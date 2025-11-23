<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();

            // Chofer que crea el ride
            $table->foreignId('user_id')
                  ->constrained() // users
                  ->onDelete('cascade');

            // Vehículo asociado
            $table->foreignId('vehiculo_id')
                  ->constrained('vehiculos')
                  ->onDelete('cascade');

            $table->string('nombre');             // Nombre del ride
            $table->string('origen');             // Lugar de salida
            $table->string('destino');            // Lugar de llegada
            $table->date('fecha');                // Día
            $table->time('hora');                 // Hora
            $table->decimal('costo_por_espacio', 10, 2);
            $table->unsignedTinyInteger('espacios'); // Cantidad de espacios

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
