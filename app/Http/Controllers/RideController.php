<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Controlador para la gestión de rides por parte de los choferes
class RideController extends Controller
{
    // Listar rides del chofer logueado
    public function index()
    {
        $user = Auth::user();

        // Rides del chofer con vehículo y reservas
        $rides = Ride::with(['vehiculo', 'reservas']) 
            ->where('user_id', $user->id)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        // Solo vehículos del chofer 
        $vehiculos = Vehiculo::where('user_id', $user->id)->get();

        return view('rides.index', compact('rides', 'vehiculos'));
    }

    // Crear nuevo ride
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nombre'           => 'required|string|max:100',
            'origen'           => 'required|string|max:100',
            'destino'          => 'required|string|max:100',
            'fecha'            => 'required|date',
            'hora'             => 'required|date_format:H:i',
            'vehiculo_id'      => 'required|exists:vehiculos,id,user_id,'.$user->id, 
            'costo_por_espacio' => 'required|numeric|min:0.01',
            'espacios'         => 'required|integer|min:1', 
        ]);

        // Validar que la fecha y hora no sean pasadas
        $fechaHoraRide = Carbon::parse($request->fecha . ' ' . $request->hora);
        $ahora = Carbon::now();

        if ($fechaHoraRide->lessThan($ahora)) {
            return back()->withInput()->withErrors('No puedes actualizar un ride a una fecha u hora pasada.');
        }

        // Validar capacidad máxima de espacios
        $vehiculo = Vehiculo::find($request->vehiculo_id);
        $max_espacios_pasajeros = $vehiculo->capacidad - 1;

        if ($request->espacios > $max_espacios_pasajeros) {
             return back()->withInput()->withErrors("No puedes asignar más espacios de los permitidos. La capacidad máxima de este vehículo es de {$max_espacios_pasajeros} pasajeros.");
        }
       
        $hora_formateada = $request->hora . ':00';

        // Validar duplicados (vehiculo_id, fecha, hora)
        $duplicado = Ride::where('user_id', $user->id)
            ->where('vehiculo_id', $request->vehiculo_id)
            ->where('fecha', $request->fecha)
            ->where('hora', $hora_formateada)
            ->exists();

        if ($duplicado) {
            return back()->withInput()->withErrors('Ya existe un ride con ese vehículo en la misma fecha y hora.');
        }

        // Crear Ride
        Ride::create(array_merge($request->all(), [
            'user_id' => $user->id,
            'hora' => $hora_formateada, 
        ]));

        return redirect()->route('rides.index')->with('success', 'Ride publicado correctamente.');
    }

    // Actualizar ride
    public function update(Request $request, Ride $ride)
    {
        $user = Auth::user();

        // Validar permiso de edición 
        if ($ride->user_id !== $user->id) {
            return back()->withErrors('No tienes permiso para editar este ride.');
        }

        // Validar que la fecha y hora no sean pasadas
        if ($ride->reservas()->whereIn('estado', [1, 2])->exists()) {
             return back()->withErrors('Este ride tiene reservas pendientes o aceptadas y no puede ser modificado.');
        }

        // Validar datos básicos
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'origen'           => 'required|string|max:100',
            'destino'          => 'required|string|max:100',
            'fecha'            => 'required|date',
            'hora'             => 'required|date_format:H:i',
            'vehiculo_id'      => 'required|exists:vehiculos,id,user_id,'.$user->id,
            'costo_por_espacio' => 'required|numeric|min:0.01',
            'espacios'         => 'required|integer|min:1', 
        ]);

        // Validar capacidad máxima de espacios
        $vehiculo = Vehiculo::find($request->vehiculo_id);
        $max_espacios_pasajeros = $vehiculo->capacidad - 1;

        if ($request->espacios > $max_espacios_pasajeros) {
             return back()->withInput()->withErrors("No puedes asignar más espacios de los permitidos. La capacidad máxima de este vehículo es de {$max_espacios_pasajeros} pasajeros.");
        }

        // Formato correcto de hora 
        $hora_formateada = $request->hora . ':00';

        // Validar duplicados solo si cambian clave única (vehiculo_id, fecha, hora)
        $claveCambiada = 
            $request->vehiculo_id != $ride->vehiculo_id      ||
            $request->fecha         != $ride->fecha            ||
            $hora_formateada      != $ride->hora;

        if ($claveCambiada) {
            $duplicado = Ride::where('user_id', $user->id)
                ->where('vehiculo_id', $request->vehiculo_id)
                ->where('fecha', $request->fecha)
                ->where('hora', $hora_formateada) 
                ->where('id', '!=', $ride->id)
                ->exists();

            if ($duplicado) {
                return back()->withErrors('Ya existe un ride con ese vehículo en la misma fecha y hora.');
            }
        }

        // Actualizar ride
        $datos = $request->except(['_method', '_token']); 
        $datos['hora'] = $hora_formateada; 

        $ride->update($datos);

        return redirect()->route('rides.index')->with('success', 'Ride actualizado correctamente.');
    }

    // Eliminar ride
    public function destroy(Ride $ride)
    {
        // No se puede eliminar si tiene reservas pendientes o aceptadas
        if ($ride->reservas()->whereIn('estado', [1, 2])->exists()) {
            return back()->withErrors('Este ride tiene reservas pendientes o aceptadas y no puede ser eliminado.');
        }

        // Solo el chofer puede eliminar su ride
        if ($ride->user_id !== Auth::id()) {
            return redirect()->route('rides.index')
                ->withErrors('No tienes permiso para eliminar este ride.');
        }

        $ride->delete();

        return redirect()->route('rides.index')
            ->with('success', 'Ride eliminado correctamente.');
    }
}
