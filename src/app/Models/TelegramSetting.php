<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramSetting extends Model
{
    protected $fillable = [
        'chat_id',
        'ping_interval',
        'is_active',
        'last_ping_at',
    ];

    protected $casts = [
        'chat_id' => 'integer',
        'ping_interval' => 'integer',
        'is_active' => 'boolean',
        'last_ping_at' => 'datetime',
    ];

    public function sites()
    {
        return Site::where('chat_id', $this->chat_id)->get();
    }
}
