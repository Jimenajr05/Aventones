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

        // CORRECCIÓN: Convertir el array de roles de strings a enteros.
        // Esto garantiza que la comparación de in_array sea correcta.
        $allowedRoles = array_map('intval', $roles);

        // Si el rol del usuario NO está permitido en esta ruta
        if (!in_array($user->role_id, $allowedRoles)) {
            // Se utiliza la variable corregida $allowedRoles
            abort(403, 'No tienes permiso para acceder a esta sección.'); 
        }

        // Si sí tiene el rol permitido
        return $next($request);
    }
}