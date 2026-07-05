<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdminKey
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('x-admin-key') !== config('webhook.master_api_key')) {
            return response()->json(['detail' => 'Invalid Admin Key'], 401);
        }

        return $next($request);
    }
}
