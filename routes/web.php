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
})->middleware(['auth', 'verified'])->name('superadmin.dashboard');

// Admin
Route::get('/admin/dashboard', function () {
    return view('dashboard.admin');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

// Chofer
Route::get('/chofer/dashboard', function () {
    return view('dashboard.chofer');
})->middleware(['auth', 'verified'])->name('chofer.dashboard');

// Pasajero
Route::get('/pasajero/dashboard', function () {
    return view('dashboard.pasajero');
})->middleware(['auth', 'verified'])->name('pasajero.dashboard');

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