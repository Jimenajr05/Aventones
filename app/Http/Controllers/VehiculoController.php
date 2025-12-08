<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Controlador para gestionar vehículos
class VehiculoController extends Controller
{
    // Listar vehículos del usuario autenticado
    public function index()
    {
        $user = auth()->user();

        $vehiculos = Vehiculo::where('user_id', $user->id)->get();

        $colores = [
            "Blanco","Negro","Gris","Plata","Azul","Rojo","Verde",
            "Amarillo","Naranja","Café","Beige","Vino","Turquesa","Morado"
        ];

        return view('vehiculos.index', compact('vehiculos', 'colores'));
    }

    // Almacenar nuevo vehículo
    public function store(Request $request)
    {
        $request->validate([
            'marca'     => 'required|string|max:50',
            'modelo'    => 'required|string|max:50',
            'placa'     => 'required|string|max:20|unique:vehiculos,placa',
            'color'     => 'required|string',
            'anio'      => 'required|integer|min:2010|max:2030',
            'capacidad' => 'required|integer|min:2|max:5',
            'fotografia'=> 'nullable|image|max:2048',
        ]);

        // Validar color permitido
        $coloresPermitidos = [
            "Blanco","Negro","Gris","Plata","Azul","Rojo","Verde",
            "Amarillo","Naranja","Café","Beige","Vino","Turquesa","Morado"
        ];

        if (!in_array($request->color, $coloresPermitidos)) {
            return back()->withErrors(['color' => 'El color seleccionado no es válido.']);
        }

        // Manejo de fotografía
        $rutaFoto = null;
        if ($request->hasFile('fotografia')) {
            $rutaFoto = $request->file('fotografia')->store('vehiculos', 'public');
        }

        Vehiculo::create([
            'user_id'   => auth()->id(),
            'marca'     => $request->marca,
            'modelo'    => $request->modelo,
            'placa'     => $request->placa,
            'color'     => $request->color,
            'anio'      => $request->anio,
            'capacidad' => $request->capacidad,
            'fotografia'=> $rutaFoto,
        ]);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo registrado correctamente.');
    }

    // Eliminar vehículo
    public function destroy(Vehiculo $vehiculo)
    {
        // Validar dueño del vehículo
        if ($vehiculo->user_id !== auth()->id()) {
            return redirect()->route('vehiculos.index')
                ->withErrors('No tienes permiso para eliminar este vehículo.');
        }

        // Revisar si tiene rides con reservas activas (pendiente=1, aceptada=2)
        $tieneReservasActivas = $vehiculo->rides()
            ->whereHas('reservas', function($q) {
                $q->whereIn('estado', [1, 2]);
            })
            ->exists();

        if ($tieneReservasActivas) {
            return redirect()->route('vehiculos.index')
                ->withErrors('No puedes eliminar este vehículo porque tiene rides con reservas activas.');
        }

        // Si NO hay reservas activas, revisar si tiene rides asociados
        $tieneRides = $vehiculo->rides()->exists();

        if ($tieneRides) {
            return redirect()->route('vehiculos.index')
                ->withErrors('Este vehículo tiene rides asociados y no puede eliminarse.');
        }

        // Si no tiene rides, permitir eliminar
        if ($vehiculo->fotografia) {
            Storage::disk('public')->delete($vehiculo->fotografia);
        }

        $vehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }

    // Actualizar vehículo
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'marca'     => 'required|string|max:50',
            'modelo'    => 'required|string|max:50',
            'placa'     => "required|string|max:20|unique:vehiculos,placa,{$vehiculo->id}",
            'color'     => 'required|string',
            'anio'      => 'required|integer|min:2010|max:2030',
            'capacidad' => 'required|integer|min:2|max:5',
            'fotografia'=> 'nullable|image|max:2048',
        ]);

        // Validar espacio suficiente para rides existentes
        $maxEspaciosRide = $vehiculo->rides()->max('espacios'); 

        if ($maxEspaciosRide !== null) {
            // Si hay rides, la capacidad debe ser al menos maxEspaciosRide + 1
            if ($request->capacidad < ($maxEspaciosRide + 1)) {
                return back()->withErrors([
                    'capacidad' => "⚠️ No puedes establecer la capacidad en {$request->capacidad}, 
                    porque tienes rides con {$maxEspaciosRide} espacios para pasajeros y se necesita al menos " .
                                ($maxEspaciosRide + 1) . " asientos totales para permitirlo."
                ]);
            }
        }

        // Actualizar foto
        if ($request->hasFile('fotografia')) {
            if ($vehiculo->fotografia) {
                Storage::disk('public')->delete($vehiculo->fotografia);
            }

            $vehiculo->fotografia = $request->file('fotografia')->store('vehiculos', 'public');
        }

        // Actualizar campos
        $vehiculo->update([
            'marca'     => $request->marca,
            'modelo'    => $request->modelo,
            'placa'     => $request->placa,
            'color'     => $request->color,
            'anio'      => $request->anio,
            'capacidad' => $request->capacidad,
        ]);

        return back()->with('success', 'Vehículo actualizado correctamente.');
    }
}
