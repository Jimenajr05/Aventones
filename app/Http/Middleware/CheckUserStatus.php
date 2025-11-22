<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si NO hay usuario logueado, continuar
        if (!$user) {
            return $next($request);
        }

        // Pendiente (1)
        if ($user->status_id == 1) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está pendiente de activación por un administrador.',
            ]);
        }

        // Inactivo (3)
        if ($user->status_id == 3) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está inactiva. Contacte a un administrador.',
            ]);
        }

        return $next($request);
    }
}
