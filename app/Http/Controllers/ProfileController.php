<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use Illuminate\View\View;
use Illuminate\Support\Str; // Asegurarse de importar Str si no está

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 🔑 CORRECCIÓN DE BUG: ASIGNAR LA VARIABLE $user
        // Esto define $user y soluciona el error 500 de "Undefined variable $user".
        $user = $request->user();
        
        $user->fill($request->validated()); // Usamos la variable $user

        
        if ($user->isDirty('email')) {
            $user->status_id = 1; // Pendiente
            $user->activation_token = Str::random(60);
        }

        $user->save(); // Usamos la variable $user

        // Redirigir a /profile
        return Redirect::to('/profile')->with('status', 'profile-updated'); 
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirigir a la raíz
        return Redirect::to('/')->with('status', 'user-deleted');
    }
}
