<?php

namespace App\MCP\Audit;

use Modules\Settings\Models\ApiLog;
use App\MCP\Contracts\MCPTool;
use Illuminate\Support\Facades\Log;
use Throwable;

class MCPAuditLogger
{
    public function log(MCPTool $tool, array $arguments, array $context, bool $success, ?string $errorMessage = null, float $duration = 0): void
    {
        try {
            if (isset($context['apiKey'])) {
                ApiLog::create([
                    'api_key_id' => $context['apiKey']->id,
                    'endpoint' => 'mcp:tool:' . $tool->name(),
                    'method' => 'MCP',
                    'status_code' => $success ? 200 : 500,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'MCP-Client',
                    'payload' => $this->sanitize($arguments),
                    'response' => $errorMessage ? ['error' => $errorMessage] : null,
                    'duration_ms' => $duration,
                ]);
            }
        } catch (Throwable $e) {
            Log::error("Failed to log MCP audit: " . $e->getMessage());
        }
    }

    private function sanitize(array $arguments): array
    {
        $sanitized = $arguments;
        $sensitiveKeys = ['password', 'secret', 'token', 'key'];
        foreach ($sanitized as $k => $v) {
            foreach ($sensitiveKeys as $sensitive) {
                if (stripos((string)$k, $sensitive) !== false) {
                    $sanitized[$k] = '***';
                }
            }
        }
        return $sanitized;
    }
}