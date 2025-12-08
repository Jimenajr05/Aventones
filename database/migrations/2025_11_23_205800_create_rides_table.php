<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migración para crear la tabla de rides
return new class extends Migration
{
    // Ejecutar las migraciones
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();

            // Chofer que crea el ride
            $table->foreignId('user_id')
                  ->constrained() 
                  ->onDelete('cascade');

            // Vehículo asociado
            $table->foreignId('vehiculo_id')
                  ->constrained('vehiculos')
                  ->onDelete('cascade');

            $table->string('nombre');             
            $table->string('origen');             
            $table->string('destino');           
            $table->date('fecha');                
            $table->time('hora');               
            $table->decimal('costo_por_espacio', 10, 2);
            $table->unsignedTinyInteger('espacios'); 

            $table->timestamps();
        });
    }

    // Revertir las migraciones
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
