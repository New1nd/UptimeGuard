<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'url',
        'name',
        'chat_id',
    ];

    protected $casts = [
        'chat_id' => 'integer',
    ];
}
