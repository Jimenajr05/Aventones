<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RideController extends Controller
{
    /**
     * Listar rides del chofer + formulario de creación
     */
    public function index()
    {
        $user = Auth::user();

        // Solo rides del chofer logueado
        // 💡 Importante: cargamos la relación 'reservas' para usar la lógica de bloqueo en la vista.
        $rides = Ride::with(['vehiculo', 'reservas']) 
            ->where('user_id', $user->id)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        // Solo vehículos del chofer (para el select)
        $vehiculos = Vehiculo::where('user_id', $user->id)->get();

        return view('rides.index', compact('rides', 'vehiculos'));
    }

    /**
     * Crear un ride
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // VALIDACIÓN: Volvemos a H:i, que es el formato exacto del input type="time"
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'origen'           => 'required|string|max:100',
            'destino'          => 'required|string|max:100',
            'fecha'            => 'required|date',
            'hora'             => 'required|date_format:H:i', // Espera 09:30 o 14:45
            'vehiculo_id'      => 'required|exists:vehiculos,id,user_id,'.$user->id, // Debe ser su vehículo
            'costo_por_espacio' => 'required|numeric|min:0.01',
            'espacios'         => 'required|integer|min:1|max:10', // Máximo 10 asientos
        ]);

        // Formato correcto de hora para guardar en la BD (HH:MM:SS)
        $hora_formateada = $request->hora . ':00';

        // VALIDACIÓN DE DUPLICADOS (un chofer no puede tener dos rides con el mismo vehículo, fecha y hora)
        $duplicado = Ride::where('user_id', $user->id)
            ->where('vehiculo_id', $request->vehiculo_id)
            ->where('fecha', $request->fecha)
            ->where('hora', $hora_formateada)
            ->exists();

        if ($duplicado) {
            return back()->withInput()->withErrors('Ya existe un ride con ese vehículo en la misma fecha y hora.');
        }

        Ride::create(array_merge($request->all(), [
            'user_id' => $user->id,
            'hora' => $hora_formateada, // Sobreescribimos con el formato H:i:s
        ]));

        return redirect()->route('rides.index')->with('success', 'Ride publicado correctamente.');
    }

    /**
     * Mostrar formulario de edición
     * (No usado, la edición es en un modal en la vista index)
     */
    // public function edit(Ride $ride)
    // {
    //     // Lógica de edición si fuera en una vista separada
    // }

    /**
     * Actualizar ride
     */
    public function update(Request $request, Ride $ride)
    {
        $user = Auth::user();

        // 1. Validar permiso de edición
        if ($ride->user_id !== $user->id) {
            return back()->withErrors('No tienes permiso para editar este ride.');
        }

        // 2. Validar bloqueo por reservas activas
        // 🛑 CORRECCIÓN: Bloquear edición si tiene reservas PENDIENTES (1) o ACEPTADAS (2)
        if ($ride->reservas()->whereIn('estado', [1, 2])->exists()) {
             return back()->withErrors('Este ride tiene reservas pendientes o aceptadas y no puede ser modificado.');
        }

        // 3. Validar datos
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'origen'           => 'required|string|max:100',
            'destino'          => 'required|string|max:100',
            'fecha'            => 'required|date',
            'hora'             => 'required|date_format:H:i',
            'vehiculo_id'      => 'required|exists:vehiculos,id,user_id,'.$user->id,
            'costo_por_espacio' => 'required|numeric|min:0.01',
            'espacios'         => 'required|integer|min:1|max:10',
        ]);

        // Formato correcto de hora para guardar en la BD (HH:MM:SS)
        $hora_formateada = $request->hora . ':00';

        // 4. Validar duplicados si se cambió la clave única (vehiculo_id, fecha, hora)
        $claveCambiada = 
            $request->vehiculo_id != $ride->vehiculo_id      ||
            $request->fecha         != $ride->fecha            ||
            // Comparamos el valor formateado con el valor de la base de datos
            $hora_formateada      != $ride->hora;

        if ($claveCambiada) {
            $duplicado = Ride::where('user_id', $user->id)
                ->where('vehiculo_id', $request->vehiculo_id)
                ->where('fecha', $request->fecha)
                ->where('hora', $hora_formateada) // Usamos el valor formateado para la consulta
                ->where('id', '!=', $ride->id)
                ->exists();

            if ($duplicado) {
                return back()->withErrors('Ya existe un ride con ese vehículo en la misma fecha y hora.');
            }
        }

        // ACTUALIZAR (usamos request->except y sobreescribimos 'hora' con el formato H:i:s)
        $datos = $request->except(['_method', '_token']); 
        $datos['hora'] = $hora_formateada; // Sobreescribimos con el formato H:i:s

        $ride->update($datos);

        return redirect()->route('rides.index')->with('success', 'Ride actualizado correctamente.');
    }

    /**
     * Eliminar ride
     */
    public function destroy(Ride $ride)
    {
        // 🛑 CORRECCIÓN: Bloquear eliminación si tiene reservas PENDIENTES (1) o ACEPTADAS (2)
        if ($ride->reservas()->whereIn('estado', [1, 2])->exists()) {
            return back()->withErrors('Este ride tiene reservas pendientes o aceptadas y no puede ser eliminado.');
        }

        // Solo el dueño puede eliminarlo
        if ($ride->user_id !== Auth::id()) {
            return redirect()->route('rides.index')
                ->withErrors('No tienes permiso para eliminar este ride.');
        }

        $ride->delete();

        return redirect()->route('rides.index')
            ->with('success', 'Ride eliminado correctamente.');
    }
}