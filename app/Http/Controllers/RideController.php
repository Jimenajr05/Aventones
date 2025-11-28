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
        $rides = Ride::with('vehiculo')
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
            'vehiculo_id'      => 'required|exists:vehiculos,id',
            'costo_por_espacio'=> 'required|numeric|min:0',
            'espacios'         => 'required|integer|min:1|max:5',
        ]);

        // Verificar que el vehículo sea del chofer logueado
        $vehiculo = Vehiculo::where('id', $request->vehiculo_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$vehiculo) {
            return back()
                ->withErrors('El vehículo seleccionado no es válido.')
                ->withInput();
        }

        // Validar espacios vs capacidad del vehículo
        if ($request->espacios > $vehiculo->capacidad) {
            return back()
                ->withErrors("No puedes asignar {$request->espacios} espacios, la capacidad del vehículo es de {$vehiculo->capacidad}.")
                ->withInput();
        }

        // ====== PRE-PROCESAMIENTO Y VALIDACIÓN DE COSTO ======
        $costo = (float) $request->costo_por_espacio;
        $fecha = Carbon::parse($request->fecha);
        
        // Formateamos la hora para asegurar H:i y los cálculos
        $carbon_hora = Carbon::createFromFormat('H:i', $request->hora);
        $hora_formateada  = $carbon_hora->format('H:i');

        // Base
        $min = 500;

        // Fin de semana: sábado (6) o domingo (0)
        if (in_array($fecha->dayOfWeek, [0, 6], true)) {
            $min = max($min, 700);
        }

        // Horario nocturno: 22:00 – 05:59
        $h = $carbon_hora->hour; // Obtenemos la hora entera directamente del objeto Carbon
        if ($h >= 22 || $h <= 5) {
            $min = max($min, 800);
        }

        if ($costo < $min) {
            return back()
                ->withErrors("El costo ingresado (₡{$costo}) es menor al mínimo permitido para ese día/horario (₡{$min}).")
                ->withInput();
        }

        if ($costo > 30000) {
            return back()
                ->withErrors("El costo ingresado excede el máximo permitido (₡30 000).")
                ->withInput();
        }

        // ====== VALIDAR RIDE DUPLICADO ======
        $existe = Ride::where('user_id', $user->id)
            ->where('vehiculo_id', $request->vehiculo_id)
            ->where('fecha', $request->fecha)
            ->where('hora', $hora_formateada) // Usamos la hora formateada
            ->exists();

        if ($existe) {
            return back()
                ->withErrors('Ya existe un ride con este vehículo en la misma fecha y hora.')
                ->withInput();
        }

        // Crear ride
        Ride::create([
            'user_id'          => $user->id,
            'vehiculo_id'      => $request->vehiculo_id,
            'nombre'           => $request->nombre,
            'origen'           => $request->origen,
            'destino'          => $request->destino,
            'fecha'            => $request->fecha,
            'hora'             => $hora_formateada, // Guardamos la hora formateada
            'costo_por_espacio'=> $costo,
            'espacios'         => $request->espacios,
        ]);

        return redirect()->route('rides.index')
            ->with('success', 'Ride creado correctamente.');
    }

    // Formulario de edición
    public function edit(Ride $ride)
    {
        if ($ride->user_id !== Auth::id()) {
            return redirect()->route('rides.index')->withErrors('No tienes permiso para editar este ride.');
        }

        $vehiculos = Vehiculo::where('user_id', Auth::id())->get();

        return view('rides.edit', compact('ride', 'vehiculos'));
    }

    // Actualizar ride
    public function update(Request $request, Ride $ride)
    {
        if ($ride->user_id !== Auth::id()) {
            return back()->withErrors('No tienes permiso para editar este ride.');
        }

        // VALIDACIÓN: Volvemos a H:i, que es el formato exacto del input type="time"
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'origen'           => 'required|string|max:100',
            'destino'          => 'required|string|max:100',
            'fecha'            => 'required|date',
            'hora'             => 'required|date_format:H:i', 
            'vehiculo_id'      => 'required|exists:vehiculos,id',
            'costo_por_espacio'=> 'required|numeric|min:0',
            'espacios'         => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();

        // Vehículo correcto
        $vehiculo = Vehiculo::where('id', $request->vehiculo_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$vehiculo) {
            return back()->withErrors('El vehículo no es válido para este usuario.');
        }

        // Capacidad
        if ($request->espacios > $vehiculo->capacidad) {
            return back()->withErrors("La capacidad máxima del vehículo es {$vehiculo->capacidad}.");
        }

        // VALIDAR COSTO (según reglas)
        $costo = $request->costo_por_espacio;
        $minimo = 500;
        $fecha = \Carbon\Carbon::parse($request->fecha);
        
        // Formateamos la hora para asegurar H:i y los cálculos
        $carbon_hora = Carbon::createFromFormat('H:i', $request->hora);
        $hora_formateada  = $carbon_hora->format('H:i');
        $h = $carbon_hora->hour; // Obtenemos la hora entera directamente del objeto Carbon

        if ($fecha->isWeekend()) $minimo = max($minimo, 700);
        if ($h >= 22 || $h <= 5) $minimo = max($minimo, 800); 

        if ($costo < $minimo) {
            return back()->withErrors("El costo mínimo permitido es ₡{$minimo}.");
        }

        if ($costo > 30000) {
            return back()->withErrors("El costo máximo permitido es ₡30 000.");
        }

        // VALIDAR DUPLICADO solo si cambió vehículo/fecha/hora
        $claveCambiada =
            $request->vehiculo_id != $ride->vehiculo_id ||
            $request->fecha       != $ride->fecha       ||
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

        // ACTUALIZAR (usamos request->except y sobreescribimos 'hora' con el formato H:i)
        $datos = $request->except(['_method', '_token']); 
        $datos['hora'] = $hora_formateada; // Sobreescribimos con el formato H:i

        $ride->update($datos);

        return redirect()->route('rides.index')->with('success', 'Ride actualizado correctamente.');
    }

    /**
     * Eliminar ride
     */
    public function destroy(Ride $ride)
    {
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