<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\RidePublicController;
use App\Http\Controllers\BuscarRideController;

//
// ---------------------------------------------------------
// PÁGINA PÚBLICA
// ---------------------------------------------------------
Route::get('/', [RidePublicController::class, 'index'])
    ->name('public.index');


//
// ---------------------------------------------------------
// DASHBOARDS POR ROL
// ---------------------------------------------------------
Route::middleware(['auth', 'verified', 'status'])->group(function () {

    // SUPER ADMIN (role:1)
    Route::get('/super-admin/dashboard', function () {
        return view('dashboard.superadmin');
    })->middleware('role:1')->name('superadmin.dashboard');

    // ADMIN (role:2)
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    })->middleware('role:2')->name('admin.dashboard');

    // CHOFER (role:3)
    Route::get('/chofer/dashboard', function () {
        return view('dashboard.chofer');
    })->middleware('role:3')->name('chofer.dashboard');

    // PASAJERO (role:4)
    Route::get('/pasajero/dashboard', function () {
        return view('dashboard.pasajero');
    })->middleware('role:4')->name('pasajero.dashboard');
});


//
// ---------------------------------------------------------
// PERFIL DE USUARIO
// ---------------------------------------------------------
Route::middleware(['auth', 'status'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


//
// ---------------------------------------------------------
// SUPER ADMIN + ADMIN — GESTIÓN DE USUARIOS
// ---------------------------------------------------------
Route::middleware(['auth', 'status', 'role:1,2'])->group(function () {

    Route::get('/admin/users', [UserManagementController::class, 'index'])
        ->name('administradores.gestionUsuarios');

    Route::post('/admin/users/{id}/activate', [UserManagementController::class, 'activate'])
        ->name('administradores.gestionUsuarios.activate');

    Route::post('/admin/users/{id}/deactivate', [UserManagementController::class, 'deactivate'])
        ->name('administradores.gestionUsuarios.deactivate');
});


//
// ---------------------------------------------------------
// ACTIVACIÓN DE CUENTA POR TOKEN
// ---------------------------------------------------------
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


//
// ---------------------------------------------------------
// CHOFER (role:3) — Vehículos & Rides
// ---------------------------------------------------------
Route::middleware(['auth', 'status', 'role:3'])->group(function () {

    // VEHÍCULOS
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::patch('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    // RIDES
    Route::get('/rides', [RideController::class, 'index'])->name('rides.index');
    Route::post('/rides', [RideController::class, 'store'])->name('rides.store');
    Route::patch('/rides/{ride}', [RideController::class, 'update'])->name('rides.update');
    Route::delete('/rides/{ride}', [RideController::class, 'destroy'])->name('rides.destroy');
});


//
// ---------------------------------------------------------
// PASAJERO (role:4) — Reservas
// ---------------------------------------------------------
Route::middleware(['auth','status','role:4'])->group(function() {
    Route::get('buscarRides/buscarRides', [BuscarRideController::class, 'vistaBuscar'])
        ->name('pasajero.buscar_rides');

    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::post('/reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])
        ->name('reservas.cancelar');

    Route::get('/mis-reservas', [ReservaController::class, 'vistaPasajero'])
        ->name('reservas.pasajero');
});


//
// ---------------------------------------------------------
// CHOFER (role:3) — Reservas recibidas
// ---------------------------------------------------------
Route::middleware(['auth','status','role:3'])->group(function() {
    Route::post('/reservas/{reserva}/aceptar', [ReservaController::class, 'aceptar'])
        ->name('reservas.aceptar');

    Route::post('/reservas/{reserva}/rechazar', [ReservaController::class, 'rechazar'])
        ->name('reservas.rechazar');

    Route::get('/reservas/chofer', [ReservaController::class, 'vistaChofer'])
        ->name('reservas.chofer');
});


//
// ---------------------------------------------------------
// REGISTRAR ADMIN (SuperAdmin y Admin) — roles 1 y 2
// ---------------------------------------------------------
Route::middleware(['auth', 'status', 'role:1,2'])->group(function () {

    Route::get('/admin/crear', [UserManagementController::class, 'createAdmin'])
        ->name('admin.create');

    Route::post('/admin/crear', [UserManagementController::class, 'storeAdmin'])
        ->name('admin.store');
});



// ---------------------------------------------------------
// Ruta para redirigir al dashboard según el rol
// ---------------------------------------------------------
Route::get('/dashboard', function () {

    $user = Auth::user();

    return match ($user->role_id) {
        1 => redirect()->route('superadmin.dashboard'),
        2 => redirect()->route('admin.dashboard'),
        3 => redirect()->route('chofer.dashboard'),
        4 => redirect()->route('pasajero.dashboard'),
        default => abort(403),
    };

})->middleware(['auth', 'verified', 'status']);
