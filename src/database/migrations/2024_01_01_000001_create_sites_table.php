<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('name')->nullable();
            $table->bigInteger('chat_id'); // Telegram chat ID (user, group or supergroup)
            $table->timestamps();

            $table->index('chat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
