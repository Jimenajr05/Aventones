<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// DASHBOARDS SEGÚN ROL

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

// SOLO SUPER ADMIN
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
