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

        // Evitar reservas duplicadas (si no está cancelada)
        $existe = Reserva::where('ride_id', $ride->id)
                        ->where('pasajero_id', auth()->id())
                        ->where('estado', '!=', 4) // NO cancelada
                        ->first();

        if ($existe) {
            return back()->withErrors('Ya tienes una reserva para este ride.');
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

        $reserva->estado = 3; // RECHAZADA
        $reserva->save();

        return back()->with('success', 'Reserva rechazada.');
    }


    // Vista del CHOFER (ver reservas recibidas)
    public function vistaChofer()
    {
        $reservas = Reserva::whereHas('ride', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->with(['pasajero', 'ride'])
        ->get();

        return view('reservas.chofer', compact('reservas'));
    }

   
    // Vista del PASAJERO (ver sus reservas y rides disponibles)
    public function vistaPasajero()
    {
        // HISTORIAL DEL PASAJERO
        $misReservas = Reserva::where('pasajero_id', auth()->id())
            ->with('ride')
            ->get();

        // RIDES DISPONIBLES (no del pasajero)
        $rides = Ride::with('vehiculo')
            ->where('user_id', '!=', auth()->id())
            ->get();

        return view('reservas.pasajero', compact('misReservas', 'rides'));
    }
}
