<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Services\UsageStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminKeyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 100);
        $skip = (int) $request->query('skip', 0);

        $keys = ApiKey::query()
            ->offset(max($skip, 0))
            ->limit(min(max($limit, 1), 100))
            ->get();

        return response()->json($keys);
    }

    public function store(Request $request, UsageStatsService $usageStats): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:api_keys,name'],
        ]);

        $rawKey = Str::random(32);

        $apiKey = ApiKey::create([
            'name' => $validated['name'],
            'key_prefix' => substr($rawKey, 0, 8),
            'hashed_key' => hash('sha256', $rawKey),
            'is_active' => true,
        ]);

        $usageStats->syncApiKeyUsage();

        return response()->json([
            'name' => $apiKey->name,
            'key' => $rawKey,
        ]);
    }

    public function destroy(ApiKey $apiKey, UsageStatsService $usageStats): JsonResponse
    {
        $apiKey->delete();
        $usageStats->syncApiKeyUsage();

        return response()->json(['ok' => true]);
    }
}
