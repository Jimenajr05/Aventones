<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ride;

class RidePublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Ride::query();

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