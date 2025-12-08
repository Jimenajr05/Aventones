<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo que representa un ride ofrecido por un chofer
class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehiculo_id',
        'nombre',
        'origen',
        'destino',
        'fecha',
        'hora',
        'costo_por_espacio',
        'espacios',
    ];

    public function chofer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
}
