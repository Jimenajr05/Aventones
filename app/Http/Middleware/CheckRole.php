<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Middleware para verificar el rol del usuario antes de permitir el acceso a ciertas rutas
class CheckRole
{
    // Metodo handle que intercepta la solicitud HTTP
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Si el usuario no está autenticado, redirigir al login
        if (!$user) {
            return redirect()->route('login');
        }

        // Convertir los roles permitidos a enteros
        $allowedRoles = array_map('intval', $roles);

        // Verificar si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->role_id, $allowedRoles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.'); 
        }

        // Si el rol es permitido, continuar con la solicitud
        return $next($request);
    }
}
