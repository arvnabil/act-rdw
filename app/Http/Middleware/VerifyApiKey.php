<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Settings\Models\ApiKey;
use Modules\Settings\Models\ApiLog;
use Illuminate\Support\Facades\Log;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $key = $request->header('X-API-KEY');

        if (!$key) {
            return response()->json(['message' => 'API Key is missing'], 401);
        }

        $apiKey = ApiKey::where('key', $key)->where('is_active', true)->first();

        if (!$apiKey) {
            return response()->json(['message' => 'Invalid or inactive API Key'], 401);
        }

        // Update last used timestamp
        $apiKey->update(['last_used_at' => now()]);

        $response = $next($request);

        $duration = (microtime(true) - $startTime) * 1000;

        // Log the request
        try {
            ApiLog::create([
                'api_key_id' => $apiKey->id,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'status_code' => $response->getStatusCode(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => $apiKey->debug_mode ? $request->all() : null,
                'response' => $apiKey->debug_mode ? json_decode($response->getContent(), true) : null,
                'duration_ms' => $duration,
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to log API request: " . $e->getMessage());
        }

        return $response;
    }
}
