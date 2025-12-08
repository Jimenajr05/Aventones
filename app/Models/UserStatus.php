<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo para representar el estado del usuario
class UserStatus extends Model
{
    protected $table = 'user_status';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
