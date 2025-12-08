<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa un rol de usuario
class Role extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
