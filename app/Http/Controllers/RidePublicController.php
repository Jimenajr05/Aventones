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
        $query = Ride::query();

        // Filtrar por origen y destino si se proporcionan en la solicitud
        if ($request->filled('origen')) {
            $query->where('origen', 'like', "%{$request->origen}%");
        }

        if ($request->filled('destino')) {
            $query->where('destino', 'like', "%{$request->destino}%");
        }

        $rides = $query->get();

        return view('public.index', compact('rides'));
    }
}
