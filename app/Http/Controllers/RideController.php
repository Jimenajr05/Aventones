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

        // VALIDACIÓN DE DATOS BÁSICOS
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'origen'           => 'required|string|max:100',
            'destino'          => 'required|string|max:100',
            'fecha'            => 'required|date',
            'hora'             => 'required|date_format:H:i', // Espera 09:30 o 14:45
            'vehiculo_id'      => 'required|exists:vehiculos,id,user_id,'.$user->id, // Debe ser su vehículo
            'costo_por_espacio' => 'required|numeric|min:0.01',
            'espacios'         => 'required|integer|min:1', // Quitamos el max:10
        ]);

        // 🛑 Validación de fecha y hora actuales
        $fechaHoraRide = Carbon::parse($request->fecha . ' ' . $request->hora);
        $ahora = Carbon::now();

        if ($fechaHoraRide->lessThan($ahora)) {
            return back()->withInput()->withErrors('No puedes actualizar un ride a una fecha u hora pasada.');
        }

        // 1. 🛑 NUEVA VALIDACIÓN: Capacidad Máxima de Espacios (Capacidad del vehículo - 1)
        $vehiculo = Vehiculo::find($request->vehiculo_id);
        $max_espacios_pasajeros = $vehiculo->capacidad - 1;

        if ($request->espacios > $max_espacios_pasajeros) {
             return back()->withInput()->withErrors("No puedes asignar más espacios de los permitidos. La capacidad máxima de este vehículo es de {$max_espacios_pasajeros} pasajeros.");
        }
        // ------------------------------------

        // Formato correcto de hora para guardar en la BD (HH:MM:SS)
        $hora_formateada = $request->hora . ':00';

        // 2. VALIDACIÓN DE DUPLICADOS (un chofer no puede tener dos rides con el mismo vehículo, fecha y hora)
        $duplicado = Ride::where('user_id', $user->id)
            ->where('vehiculo_id', $request->vehiculo_id)
            ->where('fecha', $request->fecha)
            ->where('hora', $hora_formateada)
            ->exists();

        if ($duplicado) {
            return back()->withInput()->withErrors('Ya existe un ride con ese vehículo en la misma fecha y hora.');
        }

        // 3. CREACIÓN
        Ride::create(array_merge($request->all(), [
            'user_id' => $user->id,
            'hora' => $hora_formateada, // Sobreescribimos con el formato H:i:s
        ]));

        return redirect()->route('rides.index')->with('success', 'Ride publicado correctamente.');
    }

    // Actualizar ride
    public function update(Request $request, Ride $ride)
    {
        $user = Auth::user();

        // 1. Validar permiso de edición
        if ($ride->user_id !== $user->id) {
            return back()->withErrors('No tienes permiso para editar este ride.');
        }

        // 2. Validar bloqueo por reservas activas
        // 🛑 Bloquear edición si tiene reservas PENDIENTES (1) o ACEPTADAS (2)
        if ($ride->reservas()->whereIn('estado', [1, 2])->exists()) {
             return back()->withErrors('Este ride tiene reservas pendientes o aceptadas y no puede ser modificado.');
        }

        // 3. Validar datos básicos
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'origen'           => 'required|string|max:100',
            'destino'          => 'required|string|max:100',
            'fecha'            => 'required|date',
            'hora'             => 'required|date_format:H:i',
            'vehiculo_id'      => 'required|exists:vehiculos,id,user_id,'.$user->id,
            'costo_por_espacio' => 'required|numeric|min:0.01',
            'espacios'         => 'required|integer|min:1', // Quitamos el max:10
        ]);

        // 4. 🛑 NUEVA VALIDACIÓN: Capacidad Máxima de Espacios
        $vehiculo = Vehiculo::find($request->vehiculo_id);
        $max_espacios_pasajeros = $vehiculo->capacidad - 1;

        if ($request->espacios > $max_espacios_pasajeros) {
             return back()->withInput()->withErrors("No puedes asignar más espacios de los permitidos. La capacidad máxima de este vehículo es de {$max_espacios_pasajeros} pasajeros.");
        }

        // Formato correcto de hora para guardar en la BD (HH:MM:SS)
        $hora_formateada = $request->hora . ':00';

        // 5. Validar duplicados si se cambió la clave única (vehiculo_id, fecha, hora)
        $claveCambiada = 
            $request->vehiculo_id != $ride->vehiculo_id      ||
            $request->fecha         != $ride->fecha            ||
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

        // 6. ACTUALIZAR (usamos request->except y sobreescribimos 'hora' con el formato H:i:s)
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
        // 🛑 Bloquear eliminación si tiene reservas PENDIENTES (1) o ACEPTADAS (2)
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