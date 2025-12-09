<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use Illuminate\View\View;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Storage; 

// Controlador para la gestión del perfil de usuario
class ProfileController extends Controller
{
    // Mostrar el formulario de edición del perfil
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Actualizar el perfil del usuario
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $user = $request->user();
        $user->fill($request->validated()); 

        // Cambio de foto de perfil
        if ($request->hasFile('foto')) {

            // Eliminar la foto anterior si existe
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            // Si role_id es 1 o 2 → admin, sino → usuario normal
            $carpeta = in_array($user->role_id, [1, 2]) ? 'fotos_admins' : 'fotos_usuarios';

            // Guardar la nueva foto en la carpeta correcta
            $user->foto = $request->file('foto')->store($carpeta, 'public');
        }

        // Verificar si el email ha cambiado
        if ($user->isDirty('email')) {
            
            // Si el usuario no es administrador, actualizar el estado y generar un token de activación
            if ($user->role_id != 1) { 
                $user->status_id = 1; // Pendiente
                $user->activation_token = Str::random(60);
            }
        }

        $user->save(); 

        // Redirigir al perfil con un mensaje de éxito
        return Redirect::to('/profile')->with('status', 'profile-updated'); 
    }

    // Eliminar la cuenta del usuario
    public function destroy(Request $request): RedirectResponse
    {
        // Validar la contraseña antes de eliminar la cuenta
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
