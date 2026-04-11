<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'url',
        'name',
        'user_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];
}
