<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // ¡Agregado!
use Illuminate\Support\Str; // ¡Agregado!
use Illuminate\View\View;

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

    // Aquí se maneja la lógica para actualizar el perfil del usuario
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        // 1. Actualizar datos permitidos (nombre, apellido, teléfono, etc.)
        $user->fill($request->validated());

        // 2. Si el email fue modificado, poner status pendiente nuevamente.
        if ($user->isDirty('email')) {
            $user->status_id = 1; // Pendiente
            $user->activation_token = Str::random(60);
        }

        // 3. Subir y asignar la foto si viene una nueva. ESTA PARTE VA DESPUÉS DE fill()
        if ($request->hasFile('foto')) {
            // borrar foto vieja si existe, y asegurarnos de que no sea la ruta temporal
            if ($user->foto && !str_contains($user->foto, 'xampp')) {
                Storage::disk('public')->delete($user->foto);
            }

            // Guardar la nueva foto y asignar la ruta de Storage (ej: fotos_usuarios/...)
            $user->foto = $request->file('foto')->store('fotos_usuarios', 'public');
        }

        // 4. Guardar los cambios finales en la base de datos
        $user->save();

        return back()->with('status', 'Perfil actualizado correctamente.');
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

        return Redirect::to('/');
    }
}
