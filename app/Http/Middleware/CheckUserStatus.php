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
        // 1) Si no hay usuario autenticado → continuar normal
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // 2) Si el usuario está pendiente
        if ($user->status_id == 1) {
            auth()->logout(); 
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está pendiente de activación por un administrador.',
            ]);
        }

        // 3) Si el usuario está inactivo
        if ($user->status_id == 3) {
            auth()->logout(); 
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está inactiva. Contacte a un administrador.',
            ]);
        }

        // 4) Todo OK
        return $next($request);
    }
}
