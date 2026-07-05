<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\UsageStat;

class UsageStatsService
{
    public function getOrCreate(): UsageStat
    {
        return UsageStat::firstOrCreate(
            ['id' => 1],
            [
                'total_api_keys' => ApiKey::count(),
                'total_webhooks_received' => 0,
            ],
        );
    }

    public function syncApiKeyUsage(): UsageStat
    {
        $stats = $this->getOrCreate();
        $stats->total_api_keys = ApiKey::count();
        $stats->save();

        return $stats->refresh();
    }

    public function incrementWebhookUsage(): UsageStat
    {
        $stats = $this->getOrCreate();
        $stats->increment('total_webhooks_received');

        return $stats->refresh();
    }
}
