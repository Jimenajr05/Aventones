<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa una reserva de un ride por un pasajero
class Reserva extends Model
{
    protected $fillable = [
        'ride_id',
        'pasajero_id',
        'estado'
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function pasajero()
    {
        return $this->belongsTo(User::class, 'pasajero_id');
    }
}
