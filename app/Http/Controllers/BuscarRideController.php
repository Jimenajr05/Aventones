<?php

namespace App\Http\Controllers;

use App\Models\Ride; 
use App\Models\Reserva; 
use Illuminate\Http\Request;

// Controlador para manejar la búsqueda de rides disponibles
class BuscarRideController extends Controller
{
    // Método para mostrar la vista de búsqueda de rides
    public function vistaBuscar(Request $request)
    {
        // Obtener los parámetros de búsqueda desde la solicitud
        $origen = $request->input('origen');
        $destino = $request->input('destino');
        $orden = $request->input('orden', 'fecha'); 
        $direccion = $request->input('direccion', 'asc'); 

        // Comenzar la construcción de la consulta
        $rides = Ride::query()
            ->with(['vehiculo', 'user']) 
            ->where('espacios', '>', 0);

        // Aplicar los filtros de búsqueda si se proporcionan
        if ($origen) {
            $rides->where('origen', 'like', '%' . $origen . '%');
        }

        if ($destino) {
            $rides->where('destino', 'like', '%' . $destino . '%');
        }

        // Aplicar el ordenamiento
        if (in_array($orden, ['fecha', 'origen', 'destino', 'precio', 'espacios'])) {
            $dir = strtolower($direccion) === 'desc' ? 'desc' : 'asc';

            if ($orden === 'precio') {
                $rides->orderBy('costo_por_espacio', $dir);
            } else {
                $rides->orderBy($orden, $dir);
            }

        } else {
            // Orden por defecto
            $rides->orderBy('fecha', 'asc');
        }

        // Obtener los resultados de la consulta
        $rides = $rides->get();

        // Obtener las reservas no canceladas del usuario autenticado
        $reservasNoCanceladas = Reserva::where('pasajero_id', auth()->id())
                                        ->where('estado', '!=', 4) 
                                        ->get()
                                        ->keyBy('ride_id'); 

        // Añadir la información de reserva a cada ride
        $rides = $rides->map(function ($ride) use ($reservasNoCanceladas) {
            $ride->reserva_del_pasajero = $reservasNoCanceladas->get($ride->id);
            return $ride;
        });
      
        // Añadir información sobre si alguien ha reservado el ride
        $rides = $rides->map(function ($ride) {
            $hayReserva = \App\Models\Reserva::where('ride_id', $ride->id)
                ->whereIn('estado', [1, 2])
                ->exists();

            $ride->alguien_reservo = $hayReserva;

            return $ride;
        });

        // Retornar la vista con los rides encontrados y los parámetros de búsqueda
        return view('buscarRides.buscarRides', [ 
            'rides' => $rides,
            'origen_buscado' => $origen,
            'destino_buscado' => $destino,
            'orden_actual' => $orden,
            'direccion_actual' => $direccion,
        ]);
    }
}
