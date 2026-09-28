<?php
namespace App\MCP\Audit;

use Modules\Settings\Models\ApiLog;
use App\MCP\Contracts\MCPTool;
use Illuminate\Support\Facades\Log;

class MCPAuditLogger
{
    public function log(MCPTool \, array \, array \, bool \, ?string \ = null, float \ = 0): void
    {
        try {
            if (isset(\['apiKey'])) {
                ApiLog::create([
                    'api_key_id' => \['apiKey']->id,
                    'endpoint' => 'mcp:tool:' . \->name(),
                    'method' => 'MCP',
                    'status_code' => \ ? 200 : 500,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'MCP-Client',
                    // Sanitize arguments by removing passwords/secrets if any exist
                    'payload' => \->sanitize(\),
                    'response' => \ ? ['error' => \] : null,
                    'duration_ms' => \,
                ]);
            }
        } catch (\Throwable \) {
            Log::error("Failed to log MCP audit: " . \->getMessage());
        }
    }

    private function sanitize(array \): array
    {
        \ = \;
        \ = ['password', 'secret', 'token', 'key'];
        foreach (\ as \ => \) {
            foreach (\ as \) {
                if (stripos(\, \) !== false) {
                    \[\] = '***';
                }
            }
        }
        return \;
    }
}