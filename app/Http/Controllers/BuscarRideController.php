<?php

namespace App\Http\Controllers;

use App\Models\Ride; // Asegúrate de importar tu modelo Ride
use App\Models\Reserva; // <<< PASO 1: AÑADIDO: Importar modelo Reserva
use Illuminate\Http\Request;

// 1. CORRECCIÓN: El nombre de la clase debe ser BuscarRideController
class BuscarRideController extends Controller
{
    /**
     * Muestra la página de búsqueda de rides disponibles.
     * Aplica filtros y ordenamiento a la lista de rides.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    // 2. CORRECCIÓN: El nombre del método debe ser vistaBuscar
    public function vistaBuscar(Request $request)
    {
        // 1. Obtener los valores de búsqueda del formulario (si existen)
        $origen = $request->input('origen');
        $destino = $request->input('destino');
        $orden = $request->input('orden', 'fecha'); // Por defecto, ordenar por fecha
        $direccion = $request->input('direccion', 'asc'); // Dirección por defecto: ascendente

        // 2. Iniciar la consulta al modelo Ride
        $rides = Ride::query()
            // 💡 CORRECCIÓN: Cargamos la relación 'vehiculo' y el 'user' (chofer) para evitar N/A
            ->with(['vehiculo', 'user']) 
            // Solo queremos rides que aún tengan espacios disponibles
            ->where('espacios', '>', 0);

        // 3. Aplicar filtros si los campos de origen y/o destino fueron llenados
        if ($origen) {
            $rides->where('origen', 'like', '%' . $origen . '%');
        }

        if ($destino) {
            $rides->where('destino', 'like', '%' . $destino . '%');
        }

        // 4. Aplicar el ordenamiento
        if (in_array($orden, ['fecha', 'origen', 'destino'])) {
            $dir = strtolower($direccion) === 'desc' ? 'desc' : 'asc';
            $rides->orderBy($orden, $dir);
        } else {
            // Orden por defecto si no se especifica o es inválido
            $rides->orderBy('fecha', 'asc');
        }

        // 5. Ejecutar la consulta y obtener los resultados
        $rides = $rides->get();

        // -----------------------------------------------------------------
        // <<< LÓGICA AÑADIDA PARA VALIDAR LA RESERVA DEL PASAJERO >>>
        // -----------------------------------------------------------------
        
        // A. Obtener las reservas del pasajero que NO están Canceladas (estado != 4)
        $reservasNoCanceladas = Reserva::where('pasajero_id', auth()->id())
                                        ->where('estado', '!=', 4) // Traer todas, excepto las canceladas
                                        ->get()
                                        ->keyBy('ride_id'); // Indexar por ride_id para búsqueda rápida

        // B. Adjuntar la información de reserva a cada ride bajo la propiedad 'reserva_del_pasajero'
        $rides = $rides->map(function ($ride) use ($reservasNoCanceladas) {
            // Adjuntar la reserva (si existe) o null
            $ride->reserva_del_pasajero = $reservasNoCanceladas->get($ride->id);
            return $ride;
        });
        // -----------------------------------------------------------------
        // <<< FIN LÓGICA AÑADIDA >>>
        // -----------------------------------------------------------------


        // 6. Retornar la vista con los rides
        // 3. CORRECCIÓN: Se usa 'buscarRides.buscarRides' por la estructura de carpetas
        return view('buscarRides.buscarRides', [ 
            'rides' => $rides,
            // Opcional: pasar los valores de búsqueda para que el formulario los recuerde
            'origen_buscado' => $origen,
            'destino_buscado' => $destino,
            'orden_actual' => $orden,
            'direccion_actual' => $direccion,
        ]);
    }
}