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
        Schema::table('users', function (Blueprint $table) {
            // Nuevos datos del usuario
            $table->string('nombre')->after('id');
            $table->string('apellido')->after('nombre');
            $table->string('cedula')->unique()->after('apellido');
            $table->date('fecha_nacimiento')->after('cedula');
            $table->string('telefono')->after('fecha_nacimiento');
            $table->string('foto')->nullable()->after('telefono');

            // Super admin
            $table->boolean('is_super_admin')->default(false)->after('password');

            // Relaciones
            $table->foreignId('role_id')->nullable()->after('is_super_admin')->constrained('roles');
            $table->foreignId('status_id')->nullable()->after('role_id')->constrained('user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
