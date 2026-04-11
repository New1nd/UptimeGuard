<?php

namespace App\Providers;

use App\Services\PingService;
use App\Telegram\Handlers\BotHandlers;
use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\Nutgram;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Nutgram::class, function ($app) {
            return new Nutgram(config('telegram.bot_token'));
        });

        $this->app->singleton(BotHandlers::class, function ($app) {
            return new BotHandlers($app->make(PingService::class));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole() && !config('telegram.bot_token')) {
            return;
        }

        $bot = $this->app->make(Nutgram::class);
        $handlers = $this->app->make(BotHandlers::class);
        $handlers->register($bot);
    }
}
