<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id')->unique(); // Telegram chat ID (user, group or supergroup)
            $table->integer('ping_interval')->default(60); // minutes
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_settings');
    }
};
