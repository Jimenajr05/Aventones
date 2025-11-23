<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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

        // Si cambia el email, poner status pendiente nuevamente (opcional)
        if ($user->email !== $request->email) {
            $user->status_id = 1; // Pendiente
            $user->activation_token = Str::random(60);
        }

        // Subir foto si viene nueva
        if ($request->hasFile('foto')) {
            // borrar foto vieja si existe
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $request->file('foto')->store('fotos_usuarios', 'public');
        }

        // Actualizar datos permitidos
        $user->fill($request->validated());

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
