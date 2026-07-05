<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWebhookPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;

class WebhookController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return response()->json(['error' => 'Invalid JSON']);
        }

    
        ProcessWebhookPayload::dispatch(
            is_array($body) ? $body : [],
            $request->header('source-type'),
            $this->parseIncludePdfHeader($request->header('include-pdf')),
        );

        return response()->json([
            'status' => 'accepted',
            'message' => 'Webhook accepted and processed in background.',
        ]);
    }

    private function parseIncludePdfHeader(?string $rawValue): bool
    {
        if ($rawValue === null) {
            return false;
        }

        return in_array(strtolower(trim($rawValue)), ['1', 'true', 'yes', 'on'], true);
    }
}
