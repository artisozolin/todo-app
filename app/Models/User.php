<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'generated_cookie',
        'cookie_date',
        'status',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
