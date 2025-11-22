<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    // Mostrar lista de usuarios
    public function index()
    {
        $users = User::orderBy('role_id')->get();
        return view('administradores.gestionUsuarios.index', compact('users'));
    }

    // Activar usuario
    public function activate($id)
    {
        $auth = auth()->user();   // El usuario que está intentando activar
        $user = User::findOrFail($id); // El usuario a activar

        // Nadie puede modificar al Super Admin
        if ($user->is_super_admin) {
            return back()->withErrors(['error' => 'No puedes modificar al Super Admin.']);
        }

        // Un admin NO puede activarse a sí mismo
        if ($auth->id === $user->id) {
            return back()->withErrors(['error' => 'No puedes modific tu propio estado.']);
        }

        // Admin puede activar otros admins
        $user->status_id = 2; // Activo
        $user->save();

        return back()->with('success', 'Usuario activado correctamente.');
    }

    // Desactivar usuario
    public function deactivate($id)
    {
        $auth = auth()->user();   // el que ejecuta la acción
        $user = User::findOrFail($id); // el que será desactivado

        // Nadie puede tocar al Super Admin
        if ($user->is_super_admin) {
            return back()->withErrors(['error' => 'No puedes desactivar al Super Admin.']);
        }

        // Un admin NO puede desactivarse a sí mismo
        if ($auth->id === $user->id) {
            return back()->withErrors(['error' => 'No puedes cambiar tu propio estado.']);
        }

        // Admin puede desactivar otros admins
        $user->status_id = 3; // Inactivo
        $user->save();

        return back()->with('success', 'Usuario desactivado correctamente.');
    }
}