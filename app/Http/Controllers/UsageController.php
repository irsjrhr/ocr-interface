<?php

namespace App\Http\Controllers;

use App\Services\UsageStatsService;
use Illuminate\Http\JsonResponse;

class UsageController extends Controller
{
    public function show(UsageStatsService $usageStats): JsonResponse
    {
        $stats = $usageStats->getOrCreate();

        return response()->json([
            'total_api_keys' => $stats->total_api_keys,
            'total_webhooks_received' => $stats->total_webhooks_received,
            'updated_at' => $stats->updated_at,
        ]);
    }
}
