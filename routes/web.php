<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RideController;


// Página pública principal
Route::get('/', [\App\Http\Controllers\RidePublicController::class, 'index'])
    ->name('public.index');

// Dashboards según rol

// Super Admin
Route::get('/super-admin/dashboard', function () {
    return view('dashboard.superadmin');
})->middleware(['auth', 'verified', 'role:1'])
  ->name('superadmin.dashboard');

// Admin
Route::get('/admin/dashboard', function () {
    return view('dashboard.admin');
})->middleware(['auth', 'verified', 'role:2'])
  ->name('admin.dashboard');

// Chofer
Route::get('/chofer/dashboard', function () {
    return view('dashboard.chofer');
})->middleware(['auth', 'verified', 'role:3'])
  ->name('chofer.dashboard');

// Pasajero
Route::get('/pasajero/dashboard', function () {
    return view('dashboard.pasajero');
})->middleware(['auth', 'verified', 'role:4'])
  ->name('pasajero.dashboard');

// Dashboard general (NO se usará después)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Rutas de gestión de usuarios para Super Admin
Route::middleware(['auth', 'role:1'])->group(function () {

    // Lista de usuarios
    Route::get('/super-admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])
        ->name('administradores.gestionUsuarios');

    // Activar usuario
    Route::post('/super-admin/users/{id}/activate', [\App\Http\Controllers\Admin\UserManagementController::class, 'activate'])
        ->name('administradores.gestionUsuarios.activate');

    // Desactivar usuario
    Route::post('/super-admin/users/{id}/deactivate', [\App\Http\Controllers\Admin\UserManagementController::class, 'deactivate'])
        ->name('administradores.gestionUsuarios.deactivate');
});

// Activación de cuenta vía token
Route::get('/activate/{token}', function ($token) {

    // Buscar usuario con el token
    $user = \App\Models\User::where('activation_token', $token)->first();

    // Si el token no existe o ya fue usado
    if (!$user) {
        return redirect('/login')->with(
            'status',
            'Este enlace ya fue usado o no es válido.'
        );
    }

    // Activar la cuenta
    $user->status_id = 2; // Activo
    $user->activation_token = null;
    $user->save();

    return redirect('/login')->with(
        'status',
        'Cuenta activada correctamente. Ya puedes iniciar sesión.'
    );
})->name('activate');



// RUTAS DE CHOFER (role:3)
Route::middleware(['auth', 'role:3'])->group(function () {

    // VEHÍCULOS
    Route::get('/vehiculos', [\App\Http\Controllers\VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::post('/vehiculos', [\App\Http\Controllers\VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::delete('/vehiculos/{vehiculo}', [\App\Http\Controllers\VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    // RIDES
    Route::get('/rides', [RideController::class, 'index'])->name('rides.index');
    Route::post('/rides', [RideController::class, 'store'])->name('rides.store');
    Route::patch('/rides/{ride}', [RideController::class, 'update'])->name('rides.update');
    Route::delete('/rides/{ride}', [RideController::class, 'destroy'])->name('rides.destroy');
});
