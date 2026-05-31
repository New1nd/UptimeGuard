<?php

namespace App\Console\Commands;

use App\Models\TelegramSetting;
use App\Services\PingService;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class PingScheduler extends Command
{
    protected $signature = 'ping:scheduled';
    protected $description = 'Run scheduled ping for all chats';

    public function handle(Nutgram $bot, PingService $pingService): int
    {
        $this->info('Checking scheduled pings...');

        $settings = TelegramSetting::where('is_active', true)->get();

        foreach ($settings as $setting) {
            if (!$this->shouldPing($setting)) {
                continue;
            }

            $this->info("Pinging sites for chat {$setting->chat_id}");

            $results = $pingService->pingAllSites($setting->chat_id);

            if (!empty($results)) {
                $message = "🔔 Автоматическая проверка\n\n" . $pingService->formatResults($results);
                $bot->sendMessage($message, chat_id: $setting->chat_id);
            }

            $setting->update(['last_ping_at' => now()]);
        }

        $this->info('Done');
        return Command::SUCCESS;
    }

    protected function shouldPing(TelegramSetting $setting): bool
    {
        if (!$setting->last_ping_at) {
            return true;
        }

        $secondsSinceLastPing = abs(now()->diffInSeconds($setting->last_ping_at));
        return $secondsSinceLastPing >= ($setting->ping_interval * 60);
    }
}
