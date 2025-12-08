<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Modelo para representar un vehículo
class Vehiculo extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'marca',
        'modelo',
        'placa',
        'color',
        'anio',
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