<?php

namespace App\Telegram\Handlers;

use App\Models\Site;
use App\Models\TelegramSetting;
use App\Services\PingService;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class BotHandlers
{
    public function __construct(
        protected PingService $pingService
    ) {}

    public function register(Nutgram $bot): void
    {
        $bot->onCommand('start', [$this, 'start']);
        $bot->onCommand('help', [$this, 'start']);
        $bot->onCommand('add {url}', [$this, 'add']);
        $bot->onCommand('list', [$this, 'list']);
        $bot->onCommand('remove {id}', [$this, 'remove']);
        $bot->onCommand('ping {id?}', [$this, 'ping']);
        $bot->onCommand('interval {minutes}', [$this, 'interval']);
        $bot->onCommand('status', [$this, 'status']);

        $bot->onException(function (Nutgram $bot, \Throwable $e) {
            logger()->error('Telegram bot error: ' . $e->getMessage(), ['exception' => $e]);
        });
    }

    public function start(Nutgram $bot): void
    {
        $message = "👋 Site Monitor Bot\n\n"
            . "Команды:\n"
            . "/add url [name] - Добавить сайт\n"
            . "/list - Список сайтов\n"
            . "/remove id - Удалить сайт\n"
            . "/ping - Проверить все сайты\n"
            . "/ping id - Проверить конкретный сайт\n"
            . "/interval минуты - Интервал автопроверки\n"
            . "/status - Текущие настройки";

        $bot->sendMessage($message);
    }

    public function add(Nutgram $bot, string $url): void
    {
        $userId = $bot->userId();

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            // Try adding https://
            $url = 'https://' . $url;
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $bot->sendMessage("❌ Неверный URL. Пример: /add https://example.com");
                return;
            }
        }

        $site = Site::create([
            'url' => $url,
            'name' => null,
            'user_id' => $userId,
        ]);

        // Ensure settings exist
        TelegramSetting::firstOrCreate(
            ['user_id' => $userId],
            ['ping_interval' => 60, 'is_active' => true]
        );

        $bot->sendMessage("✅ Сайт добавлен: {$url}\nID: {$site->id}");
    }

    public function list(Nutgram $bot): void
    {
        $userId = $bot->userId();
        $sites = Site::where('user_id', $userId)->get();

        if ($sites->isEmpty()) {
            $bot->sendMessage("📋 Список пуст. Добавьте сайт: /add <url>");
            return;
        }

        $lines = ["📋 Ваши сайты:\n"];
        foreach ($sites as $site) {
            $name = $site->name ?: $site->url;
            $lines[] = "• [{$site->id}] {$name}\n  {$site->url}";
        }

        $bot->sendMessage(implode("\n", $lines));
    }

    public function remove(Nutgram $bot, string $id): void
    {
        $userId = $bot->userId();
        $site = Site::where('id', $id)->where('user_id', $userId)->first();

        if (!$site) {
            $bot->sendMessage("❌ Сайт не найден");
            return;
        }

        $name = $site->name ?: $site->url;
        $site->delete();

        $bot->sendMessage("🗑 Удалён: {$name}");
    }

    public function ping(Nutgram $bot, ?string $id = null): void
    {
        $userId = $bot->userId();

        if ($id) {
            $site = Site::where('id', $id)->where('user_id', $userId)->first();
            if (!$site) {
                $bot->sendMessage("❌ Сайт не найден");
                return;
            }

            $bot->sendMessage("⏳ Проверяю...");
            $result = $this->pingService->pingSite($site);
            $bot->sendMessage($this->pingService->formatResult($result));
        } else {
            $bot->sendMessage("⏳ Проверяю все сайты...");
            $results = $this->pingService->pingAllSites($userId);
            $bot->sendMessage($this->pingService->formatResults($results));

            // Update last ping time
            TelegramSetting::where('user_id', $userId)->update(['last_ping_at' => now()]);
        }
    }

    public function interval(Nutgram $bot, string $minutes): void
    {
        $userId = $bot->userId();
        $minutes = (int) $minutes;

        if ($minutes < 1 || $minutes > 1440) {
            $bot->sendMessage("❌ Интервал должен быть от 1 до 1440 минут (24 часа)");
            return;
        }

        TelegramSetting::updateOrCreate(
            ['user_id' => $userId],
            ['ping_interval' => $minutes]
        );

        $bot->sendMessage("⏰ Интервал автопроверки: {$minutes} мин.");
    }

    public function status(Nutgram $bot): void
    {
        $userId = $bot->userId();
        $settings = TelegramSetting::where('user_id', $userId)->first();
        $sitesCount = Site::where('user_id', $userId)->count();

        $interval = $settings?->ping_interval ?? 60;
        $isActive = $settings?->is_active ?? true;
        $lastPing = $settings?->last_ping_at?->format('d.m.Y H:i') ?? 'никогда';
        $statusEmoji = $isActive ? '🟢' : '🔴';

        $message = "⚙️ Настройки\n\n"
            . "📋 Сайтов: {$sitesCount}\n"
            . "⏰ Интервал: {$interval} мин.\n"
            . "{$statusEmoji} Автопроверка: " . ($isActive ? 'вкл' : 'выкл') . "\n"
            . "🕐 Последняя проверка: {$lastPing}";

        $bot->sendMessage($message);
    }
}
