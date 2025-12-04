<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use Illuminate\View\View;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Storage; // 💡 Importar Storage para manejar archivos

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

        $user = $request->user();
        $user->fill($request->validated()); 

        
        // 🎯 LÓGICA DE GESTIÓN DE LA FOTO (CORRECCIÓN CLAVE)
        if ($request->hasFile('foto')) {
            
            // 1. Eliminar la foto anterior si existe
            if ($user->foto) {
                // Asumiendo que las fotos se guardan en el disco 'public'
                Storage::disk('public')->delete($user->foto); 
            }
            
            // 2. Guardar la nueva foto
            // Esto guarda el archivo y devuelve la ruta relativa (e.g., 'avatars/nombre_hash.jpg')
            $user->foto = $request->file('foto')->store('avatars', 'public');
        }


        // 🎯 LÓGICA DE CAMBIO DE EMAIL (para Super Admin no desactivar)
        if ($user->isDirty('email')) {
            
            // Solo si el usuario NO es Super Admin (role_id != 1), se pone como Pendiente (1)
            if ($user->role_id != 1) { 
                $user->status_id = 1; // Pendiente
                $user->activation_token = Str::random(60);
            }
        }

        $user->save(); 

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
        
        // Antes de eliminar el usuario, eliminamos su foto para liberar espacio.
        if ($user->foto) {
             Storage::disk('public')->delete($user->foto);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirigir a la raíz
        return Redirect::to('/'); 
    }
}
