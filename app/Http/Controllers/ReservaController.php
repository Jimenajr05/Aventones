<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Ride;

// Controlador para gestionar reservas de rides
class ReservaController extends Controller
{
    // Método para crear una nueva reserva
    public function store(Request $request)
    {
        // Validar entrada
        $request->validate([
            'ride_id' => 'required|exists:rides,id'
        ]);

        $ride = Ride::findOrFail($request->ride_id);

        // Verificar si ya existe una reserva activa o pendiente para este ride y pasajero
        $existe = Reserva::where('ride_id', $ride->id)
                        ->where('pasajero_id', auth()->id())
                        ->whereIn('estado', [1, 2]) // Solo bloquea si está activa (Pendiente o Aceptada)
                        ->first();

        // Manejo adecuado de respuestas para API y Web
        if ($existe) {
            if ($request->expectsJson()) {
                 return response()->json([
                     'message' => 'No se pudo crear la reserva. Verifique la disponibilidad u otros requisitos.'
                 ], 400);
            }
            return back()->withErrors('Ya tienes una reserva activa o pendiente para este ride.');
        }

        // Crear la reserva
        Reserva::create([
            'ride_id' => $ride->id,
            'pasajero_id' => auth()->id(),
            'estado' => 1 // Pendiente
        ]);

        // Manejo adecuado de respuestas para API y Web
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Reserva creada con éxito'], 201);
        }

        return back()->with('success', 'Reserva creada correctamente.');
    }

    // Método para cancelar una reserva
    public function cancelar(Reserva $reserva)
    {
        if ($reserva->pasajero_id !== auth()->id()) {
            abort(403);
        }

        $reserva->estado = 4; // Cancelada
        $reserva->save();

        // Manejo adecuado de respuestas para API y Web
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Reserva cancelada con éxito'], 200);
        }

        return back()->with('success', 'Reserva cancelada.');
    }

    // Método para que el chofer acepte una reserva
    public function aceptar(Reserva $reserva)
    {
        $ride = $reserva->ride;

        if ($ride->user_id !== auth()->id()) { // chofer dueño del ride
            abort(403);
        }

        $reserva->estado = 2; // Aceptada
        $reserva->save();

        // Manejo adecuado de respuestas para API y Web
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Reserva aceptada con éxito'], 200);
        }

        return back()->with('success', 'Reserva aceptada.');
    }

    // Método para que el chofer rechace una reserva
    public function rechazar(Reserva $reserva)
    {
        $ride = $reserva->ride;

        if ($ride->user_id !== auth()->id()) {
            abort(403);
        }

        $reserva->estado = 3; // Rechazada
        $reserva->save();
        
        // Manejo adecuado de respuestas para API y Web
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Reserva rechazada con éxito'], 200);
        }

        return back()->with('success', 'Reserva rechazada.');
    }

    // Método para mostrar la vista del chofer
    public function vistaChofer()
    {
        // Obtener todas las reservas relacionadas con los rides del chofer autenticado
        $todasLasReservas = Reserva::whereHas('ride', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->with(['pasajero', 'ride']) // Se cargan Pasajero y Ride
        ->get();

        // Solicitudes recibidas (Pendientes: estado=1)
        $solicitudesRecibidas = $todasLasReservas->filter(function ($reserva) {
            return $reserva->estado == 1;
        });

        // Historial de reservas (Aceptadas=2, Rechazadas=3, Canceladas=4)
        $historialReservasChofer = $todasLasReservas->filter(function ($reserva) {
            return in_array($reserva->estado, [2, 3, 4]);
        });

        // Pasar ambas colecciones a la vista
        return view('reservas.chofer', compact('solicitudesRecibidas', 'historialReservasChofer'));
    }

    // Método para mostrar la vista del pasajero
    public function vistaPasajero()
    {
        // Obtener todas las reservas del pasajero autenticado
        $todasLasReservas = Reserva::where('pasajero_id', auth()->id())
            ->with(['ride.user', 'ride.vehiculo']) 
            ->get();

        // Reservas activas (Pendientes=1, Aceptadas=2)
        $misReservas = $todasLasReservas->filter(function ($reserva) {
            return in_array($reserva->estado, [1, 2]);
        });
        
        // Historial de reservas (Rechazadas=3, Canceladas=4)
        $historialReservas = $todasLasReservas->filter(function ($reserva) {
            return in_array($reserva->estado, [3, 4]);
        });

        // Obtener todos los rides disponibles (excluyendo los del pasajero)
        $rides = Ride::with(['vehiculo', 'user']) 
            ->where('user_id', '!=', auth()->id())
            ->get();

        // Obtener reservas del pasajero que no estén canceladas
        $reservasNoCanceladas = Reserva::where('pasajero_id', auth()->id())
                                        ->where('estado', '!=', 4) // Traer todas, excepto las canceladas
                                        ->get()
                                        ->keyBy('ride_id'); 

        // Marcar en cada ride si el pasajero ya tiene una reserva activa o pendiente
        $rides = $rides->map(function ($ride) use ($reservasNoCanceladas) {
            $ride->reserva_del_pasajero = $reservasNoCanceladas->get($ride->id);
            return $ride;
        });

        // Se pasan las tres colecciones a la vista
        return view('reservas.pasajero', compact('misReservas', 'historialReservas', 'rides'));
    }
}
