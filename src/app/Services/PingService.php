<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Http;

class PingService
{
    public function pingSite(Site $site): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(10)->get($site->url);
            $responseTime = round((microtime(true) - $startTime) * 1000);

            return [
                'site' => $site,
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'site' => $site,
                'success' => false,
                'status_code' => null,
                'response_time' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function pingAllSites(int $userId): array
    {
        $sites = Site::where('user_id', $userId)->get();
        $results = [];

        foreach ($sites as $site) {
            $results[] = $this->pingSite($site);
        }

        return $results;
    }

    public function formatResult(array $result): string
    {
        $site = $result['site'];
        $name = $site->name ?: $site->url;

        if ($result['success']) {
            return "✅ {$name}\n   {$result['status_code']} • {$result['response_time']}ms";
        }

        $error = $result['error'] ?? "HTTP {$result['status_code']}";
        return "❌ {$name}\n   {$error}";
    }

    public function formatResults(array $results): string
    {
        if (empty($results)) {
            return "📋 Список сайтов пуст. Добавьте сайт командой /add";
        }

        $lines = ["📊 Результаты проверки:\n"];

        foreach ($results as $result) {
            $lines[] = $this->formatResult($result);
        }

        $successful = count(array_filter($results, fn($r) => $r['success']));
        $total = count($results);

        $lines[] = "\n📈 Итого: {$successful}/{$total} доступны";

        return implode("\n", $lines);
    }
}
