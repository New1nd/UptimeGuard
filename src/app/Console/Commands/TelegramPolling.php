<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling';
    protected $description = 'Start Telegram bot long polling';

    public function handle(Nutgram $bot): int
    {
        $this->info('Starting Telegram bot polling...');
        $this->info('Press Ctrl+C to stop');

        $bot->run();

        return Command::SUCCESS;
    }
}
