<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirección según rol
        $user = Auth::user();

        if ($user->role_id == 1) { // super_admin
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->role_id == 2) { // admin
            return redirect()->route('admin.dashboard');
        }

        if ($user->role_id == 3) { // chofer
            return redirect()->route('chofer.dashboard');
        }

        if ($user->role_id == 4) { // pasajero
            return redirect()->route('pasajero.dashboard');
        }

        // fallback
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
