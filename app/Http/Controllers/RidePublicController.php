<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ride;

// Controlador para la vista pública de los viajes
class RidePublicController extends Controller
{
    // Muestra la lista de viajes disponibles
    public function index(Request $request)
    {
        // Ordenamiento
        $orden = $request->input('orden', 'fecha');      // por defecto: fecha
        $direccion = $request->input('direccion', 'asc'); // por defecto: asc

        // Construir query con filtros
        $rides = Ride::query()
            ->when($request->filled('origen'), function ($q) use ($request) {
                $q->where('origen', 'like', "%{$request->origen}%");
            })
            ->when($request->filled('destino'), function ($q) use ($request) {
                $q->where('destino', 'like', "%{$request->destino}%");
            })
            ->orderBy($orden, $direccion) // Ordenar por el campo elegido
            ->with(['vehiculo'])          // Cargar relación del vehículo
            ->get();

        return view('public.index', compact('rides'));
    }
}
