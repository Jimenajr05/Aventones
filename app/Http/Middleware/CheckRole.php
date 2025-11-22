<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Si no hay usuario, continuar flujo normal (será bloqueado por 'auth')
        if (!$user) {
            return $next($request);
        }

        // Si el rol del usuario NO está en la lista permitida
        if (!in_array($user->role_id, $roles)) {
            return redirect()->route('dashboard')
                ->withErrors(['access' => 'No tienes permiso para acceder a esta sección.']);
        }

        return $next($request);
    }
}
