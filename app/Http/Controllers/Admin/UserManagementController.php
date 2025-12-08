<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Controlador para la gestión de usuarios por parte de administradores
class UserManagementController extends Controller
{
    // Mostrar la lista completa de usuarios
    public function index()
    {
        $users = User::orderBy('role_id')->get();
        return view('administradores.gestionUsuarios.index', compact('users'));
    }

    // Mostrar el formulario para crear un nuevo administrador
    public function createAdmin()
    {
        return view('administradores.gestionUsuarios.registroAdmin');
    }

    // Almacenar un nuevo administrador en la base de datos
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

        // Crear el administrador en la base de datos con role_id fijo de 2
        User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'foto' => $rutaFoto,
            'role_id' => 2,      
            'status_id' => 2,  
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Administrador creado correctamente.');
    }

    // Actuvar usuario 
    public function activate($id)
    {
        $auth = auth()->user();   
        $user = User::findOrFail($id); 

        // Nadie puede modificar al Super Admin
        if ($user->role_id == 1 && $user->is_super_admin) {
            return back()->withErrors(['error' => 'No puedes modificar al Super Admin.']);
        }

        // Activar usuario
        $user->status_id = 2; // Activo
        $user->save();

        return back()->with('success', 'Usuario activado correctamente.');
    }

    // Desactivar usuario
    public function deactivate($id)
    {
        $auth = auth()->user();
        $user = User::findOrFail($id);

        // Nadie puede modificar al Super Admin
        if ($user->role_id == 1 && $user->is_super_admin) {
            return back()->withErrors(['error' => 'No puedes desactivar al Super Admin.']);
        }

        // Los usuarios no pueden desactivarse a sí mismos
        if ($auth->id === $user->id) {
            return back()->withErrors(['error' => 'No puedes desactivarte tú mismo.']);
        }

        // Desactivar usuario
        $user->status_id = 3; // Inactivo
        $user->save();

        return back()->with('success', 'Usuario desactivado correctamente.');
    }
}
