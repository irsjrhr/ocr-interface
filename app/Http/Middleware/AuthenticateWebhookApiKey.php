<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWebhookApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-api-key');

        if (! is_string($apiKey) || $apiKey === '') {
            return response()->json(['detail' => 'Could not validate credentials'], 403);
        }

        $key = ApiKey::query()
            ->where('hashed_key', hash('sha256', $apiKey))
            ->where('is_active', true)
            ->first();

        if (! $key) {
            return response()->json(['detail' => 'Could not validate credentials'], 403);
        }

        $request->attributes->set('api_key', $key);

        return $next($request);
    }
}
