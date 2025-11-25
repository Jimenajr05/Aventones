<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Si no está autenticado → login
        if (!$user) {
            return redirect()->route('login');
        }

        // Si el rol del usuario NO está permitido en esta ruta
        if (!in_array($user->role_id, $roles)) {

            // Redirigir según el rol REAL del usuario
            switch ($user->role_id) {

                // SUPER ADMIN
                case 1:
                    return redirect()
                        ->route('superadmin.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);

                // ADMIN
                case 2:
                    return redirect()
                        ->route('admin.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);

                // CHOFER
                case 3:
                    return redirect()
                        ->route('chofer.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);

                // PASAJERO
                case 4:
                    return redirect()
                        ->route('pasajero.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);
            }
        }

        // Si sí tiene el rol permitido
        return $next($request);
    }
}