<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $table = 'user_status';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
