<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $fillable = [
        'user_id',
        'marca',
        'modelo',
        'placa',
        'color',
        'anno',
        'capacidad',
        'fotografia',
    ];

    public function chofer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

}