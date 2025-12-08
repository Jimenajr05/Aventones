<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

// Middleware para verificar el estado del usuario antes de permitir el acceso a ciertas rutas
class CheckUserStatus
{
    // Metodo handle que intercepta la solicitud HTTP
    public function handle(Request $request, Closure $next)
    {
        // Si no hay usuario autenticado, continuar normal
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Si el usuario está pendiente
        if ($user->status_id == 1) {
            auth()->logout(); 
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está pendiente de activación por un administrador.',
            ]);
        }

        // Si el usuario está inactivo
        if ($user->status_id == 3) {
            auth()->logout(); 
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está inactiva. Contacte a un administrador.',
            ]);
        }

        // Si el usuario está activo, continuar con la solicitud
        return $next($request);
    }
}
