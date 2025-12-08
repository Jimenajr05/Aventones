<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\RidePublicController;
use App\Http\Controllers\BuscarRideController;
use App\Http\Controllers\Admin\AdminTaskController; 

// Ruta pública, página de inicio
Route::get('/', [RidePublicController::class, 'index'])
    ->name('public.index');

// Rutas protegidas por autenticación, verificación y estado activo
Route::middleware(['auth', 'verified', 'status'])->group(function () {

    // Super Admin (role:1)
    Route::get('/super-admin/dashboard', function () {
        return view('dashboard.superadmin');
    })->middleware('role:1')->name('superadmin.dashboard');

    // Amin (role:2)
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    })->middleware('role:2')->name('admin.dashboard');

    // Chofer (role:3)
    Route::get('/chofer/dashboard', function () {
        return view('dashboard.chofer');
    })->middleware('role:3')->name('chofer.dashboard');

    // Pajero (role:4)
    Route::get('/pasajero/dashboard', function () {
        return view('dashboard.pasajero');
    })->middleware('role:4')->name('pasajero.dashboard');
});

// Rutas de perfil de usuario
Route::middleware(['auth', 'status'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas para la gestión de usuarios por Super Admin y Admin (roles 1 y 2)
Route::middleware(['auth', 'status', 'role:1,2'])->group(function () {

    Route::get('/admin/users', [UserManagementController::class, 'index'])
        ->name('administradores.gestionUsuarios');

    // Activar y desactivar usuarios
    Route::post('/admin/users/{id}/activate', [UserManagementController::class, 'activate'])
        ->name('administradores.gestionUsuarios.activate');

    Route::post('/admin/users/{id}/deactivate', [UserManagementController::class, 'deactivate'])
        ->name('administradores.gestionUsuarios.deactivate');

    // Rutas para tareas administrativas, notificar reservas
    Route::post('/admin/ejecutar-comando/notificar-reservas', [AdminTaskController::class, 'executeReservationReminder'])
        ->name('admin.execute.reservation_reminder');
});

// Ruta para activar cuenta mediante token
Route::get('/activate/{token}', function ($token) {

    $user = \App\Models\User::where('activation_token', $token)->first();

    if (!$user) {
        return redirect('/login')->with('status', 'Este enlace ya fue usado o no es válido.');
    }

    $user->status_id = 2;
    $user->activation_token = null;
    $user->save();

    return redirect('/login')->with('status', 'Cuenta activada correctamente. Ya puedes iniciar sesión.');
})->name('activate');

// Rutas para Chofer (role:3) — Gestión de Vehículos y Rides
Route::middleware(['auth', 'status', 'role:3'])->group(function () {

    // Vehículos
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::patch('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    // Rides
    Route::get('/rides', [RideController::class, 'index'])->name('rides.index');
    Route::post('/rides', [RideController::class, 'store'])->name('rides.store');
    Route::patch('/rides/{ride}', [RideController::class, 'update'])->name('rides.update');
    Route::delete('/rides/{ride}', [RideController::class, 'destroy'])->name('rides.destroy');
});

// Rutas para Pasajero (role:4) — Buscar Rides y Reservas
Route::middleware(['auth','status','role:4'])->group(function() {
    Route::get('buscarRides/buscarRides', [BuscarRideController::class, 'vistaBuscar'])
        ->name('pasajero.buscar_rides');

    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::post('/reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])
        ->name('reservas.cancelar');

    Route::get('/mis-reservas', [ReservaController::class, 'vistaPasajero'])
        ->name('reservas.pasajero');
});

// Rutas para Chofer (role:3) — Gestionar Reservas
Route::middleware(['auth','status','role:3'])->group(function() {
    Route::post('/reservas/{reserva}/aceptar', [ReservaController::class, 'aceptar'])
        ->name('reservas.aceptar');

    Route::post('/reservas/{reserva}/rechazar', [ReservaController::class, 'rechazar'])
        ->name('reservas.rechazar');

    Route::get('/reservas/chofer', [ReservaController::class, 'vistaChofer'])
        ->name('reservas.chofer');
});

// Rutas para la creación de nuevos administradores por Super Admin y Admin (roles 1 y 2)
Route::middleware(['auth', 'status', 'role:1,2'])->group(function () {

    Route::get('/admin/crear', [UserManagementController::class, 'createAdmin'])
        ->name('admin.create');

    Route::post('/admin/crear', [UserManagementController::class, 'storeAdmin'])
        ->name('admin.store');
});

// Ruta para redirigir al dashboard según el rol del usuario
Route::get('/dashboard', function () {

    $user = Auth::user();

    return match ($user->role_id) {
        1 => redirect()->route('superadmin.dashboard'),
        2 => redirect()->route('admin.dashboard'),
        3 => redirect()->route('chofer.dashboard'),
        4 => redirect()->route('pasajero.dashboard'),
        default => abort(403),
    };

})->middleware(['auth', 'verified', 'status'])->name('dashboard');
