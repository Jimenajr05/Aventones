<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // Mostrar la lista completa de usuarios
    public function index()
    {
        $users = User::orderBy('role_id')->get();
        return view('administradores.gestionUsuarios.index', compact('users'));
    }

    //  REGISTRAR NUEVO ADMIN (rol_id = 2)
    public function createAdmin()
    {
        return view('administradores.gestionUsuarios.registroAdmin');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cedula' => 'required|string|max:20',
            'fecha_nacimiento' => ['required', 'date', function ($attribute, $value, $fail) {
                if (\Carbon\Carbon::parse($value)->age < 18) {
                    $fail('El administrador debe ser mayor de 18 años.');
                }
            }],
            'telefono' => 'required|string|max:20|unique:users,telefono',
            'password' => 'required|string|min:8|confirmed',
        ]);


        // Subir foto
        $rutaFoto = null;
        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('fotos_admins', 'public');
        }

        // Crear siempre ADMIN (role_id = 1)
        User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'foto' => $rutaFoto,
            'role_id' => 2,      // 🔥 ADMIN FIJO
            'status_id' => 2,    // Activo
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Administrador creado correctamente.');
    }

    //  ACTIVAR USUARIO
    public function activate($id)
    {
        $auth = auth()->user();   
        $user = User::findOrFail($id); 

        // Nadie puede tocar al Super Admin
        if ($user->role_id == 1 && $user->is_super_admin) {
            return back()->withErrors(['error' => 'No puedes modificar al Super Admin.']);
        }

        // Activar usuario
        $user->status_id = 2; // Activo
        $user->save();

        return back()->with('success', 'Usuario activado correctamente.');
    }

    //  DESACTIVAR USUARIO
    public function deactivate($id)
    {
        $auth = auth()->user();
        $user = User::findOrFail($id);

        // Nadie puede tocar al Super Admin
        if ($user->role_id == 1 && $user->is_super_admin) {
            return back()->withErrors(['error' => 'No puedes desactivar al Super Admin.']);
        }

        // No puede desactivarse a sí mismo
        if ($auth->id === $user->id) {
            return back()->withErrors(['error' => 'No puedes desactivarte tú mismo.']);
        }

        // Desactivar usuario
        $user->status_id = 3; // Inactivo
        $user->save();

        return back()->with('success', 'Usuario desactivado correctamente.');
    }
}