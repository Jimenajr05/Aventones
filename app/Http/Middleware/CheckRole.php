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

        if (!$user) {
            return redirect()->route('login');
        }

        // Si el rol NO está permitido en esta ruta
        if (!in_array($user->role_id, $roles)) {

            // Redirigir al dashboard correcto según rol_id
            switch ($user->role_id) {

                case 1:
                    return redirect()->route('superadmin.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);

                case 2:
                    return redirect()->route('admin.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);

                case 3:
                    return redirect()->route('chofer.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);

                case 4:
                    return redirect()->route('pasajero.dashboard')
                        ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);
            }
        }

        return $next($request);
    }
}