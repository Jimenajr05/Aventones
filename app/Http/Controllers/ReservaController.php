<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Ride;

class ReservaController extends Controller
{
    // ---------------------------
    // PASAJERO CREA RESERVA
    // ---------------------------
    public function store(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|exists:rides,id'
        ]);

        $ride = Ride::findOrFail($request->ride_id);

        // Evitar reservas duplicadas: solo si está PENDIENTE (1) o ACEPTADA (2)
        $existe = Reserva::where('ride_id', $ride->id)
                        ->where('pasajero_id', auth()->id())
                        ->whereIn('estado', [1, 2]) // Solo bloquea si está activa (Pendiente o Aceptada)
                        ->first();

        if ($existe) {
            return back()->withErrors('Ya tienes una reserva activa o pendiente para este ride.');
        }

        Reserva::create([
            'ride_id' => $ride->id,
            'pasajero_id' => auth()->id(),
            'estado' => 1 // PENDIENTE
        ]);

        return back()->with('success', 'Reserva creada correctamente.');
    }

    // ---------------------------
    // PASAJERO CANCELA RESERVA
    // ---------------------------
    public function cancelar(Reserva $reserva)
    {
        if ($reserva->pasajero_id !== auth()->id()) {
            abort(403);
        }

        $reserva->estado = 4; // CANCELADA
        $reserva->save();

        return back()->with('success', 'Reserva cancelada.');
    }

    // ---------------------------
    // CHOFER ACEPTA RESERVA
    // ---------------------------
    public function aceptar(Reserva $reserva)
    {
        $ride = $reserva->ride;

        if ($ride->user_id !== auth()->id()) { // chofer dueño del ride
            abort(403);
        }

        $reserva->estado = 2; // ACEPTADA
        $reserva->save();

        return back()->with('success', 'Reserva aceptada.');
    }

    // ---------------------------
    // CHOFER RECHAZA RESERVA
    // ---------------------------
    public function rechazar(Reserva $reserva)
    {
        $ride = $reserva->ride;

        if ($ride->user_id !== auth()->id()) {
            abort(403);
        }

        $reserva->estado = 3; // RECHAZADA. Esto activará el botón de Reservar nuevamente.
        $reserva->save();

        return back()->with('success', 'Reserva rechazada.');
    }


    // -----------------------------------------------------------------
    // VISTA DEL CHOFER (Separada en Solicitudes e Historial)
    // -----------------------------------------------------------------
    public function vistaChofer()
    {
        // 1. Obtener todas las reservas que pertenecen a los rides del Chofer
        $todasLasReservas = Reserva::whereHas('ride', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->with(['pasajero', 'ride']) // Se cargan Pasajero y Ride
        ->get();

        // 2. SOLICITUDES RECIBIDAS (Activas: Solo Pendientes=1)
        $solicitudesRecibidas = $todasLasReservas->filter(function ($reserva) {
            return $reserva->estado == 1;
        });

        // 3. HISTORIAL (Terminadas: Aceptada=2, Rechazada=3, Cancelada=4)
        $historialReservasChofer = $todasLasReservas->filter(function ($reserva) {
            return in_array($reserva->estado, [2, 3, 4]);
        });

        // Pasar ambas colecciones a la vista
        return view('reservas.chofer', compact('solicitudesRecibidas', 'historialReservasChofer'));
    }

   
    // -----------------------------------------------------------------
    // VISTA DEL PASAJERO (Separada en Mis Reservas e Historial)
    // -----------------------------------------------------------------
    public function vistaPasajero()
    {
        // Cargar todas las reservas con sus relaciones necesarias (Ride, Chofer, Vehículo)
        $todasLasReservas = Reserva::where('pasajero_id', auth()->id())
            // CORRECCIÓN CLAVE: Cargar ride.user (Chofer) y ride.vehiculo
            ->with(['ride.user', 'ride.vehiculo']) 
            ->get();

        // 1. MIS RESERVAS (Activas: Pendiente=1, Aceptada=2)
        $misReservas = $todasLasReservas->filter(function ($reserva) {
            return in_array($reserva->estado, [1, 2]);
        });
        
        // 2. HISTORIAL (Terminadas: Rechazada=3, Cancelada=4)
        $historialReservas = $todasLasReservas->filter(function ($reserva) {
            return in_array($reserva->estado, [3, 4]);
        });


        // RIDES DISPONIBLES (no del pasajero)
        $rides = Ride::with(['vehiculo', 'user']) // Se incluye 'user' para el Chofer
            ->where('user_id', '!=', auth()->id())
            ->get();

        // OBTENER LAS RESERVAS DEL PASAJERO (NO CANCELADAS = estado != 4)
        // Esto es clave para validar el botón.
        $reservasNoCanceladas = Reserva::where('pasajero_id', auth()->id())
                                        ->where('estado', '!=', 4) // Traer todas, excepto las canceladas
                                        ->get()
                                        ->keyBy('ride_id'); // Indexar por ride_id para búsqueda rápida

        // ADJUNTAR LA INFORMACIÓN DE RESERVA A CADA RIDE
        // Recorrer los rides disponibles y agregar la reserva si existe.
        $rides = $rides->map(function ($ride) use ($reservasNoCanceladas) {
            // Adjuntar la reserva (si existe) o null
            $ride->reserva_del_pasajero = $reservasNoCanceladas->get($ride->id);
            return $ride;
        });

        // Se pasan las tres colecciones a la vista
        return view('reservas.pasajero', compact('misReservas', 'historialReservas', 'rides'));
    }
}
