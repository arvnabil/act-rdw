<?php
namespace App\MCP\Server;

use App\MCP\Auth\MCPAuthorizer;
use App\MCP\Audit\MCPAuditLogger;
use App\MCP\Contracts\MCPTool;
use Exception;

class ToolDispatcher
{
    public function __construct(
        private ToolRegistry \,
        private MCPAuthorizer \,
        private MCPAuditLogger \
    ) {}

    public function dispatch(string \, array \, array \): mixed
    {
        \ = \->registry->get(\);
        if (!\) {
            throw new Exception("Unknown tool: \", 404); // Using 404 for unknown tool mapping to internal error
        }

        if (!\->authorizer->authorize(\->capability(), \)) {
            \->auditLogger->log(\, \, \, false, "Unauthorized");
            throw new Exception("Unauthorized capability: " . \->capability(), 403);
        }

        \->validateSchema(\->inputSchema(), \);

        \ = microtime(true);
        try {
            \ = \->execute(\, \);
            \ = (microtime(true) - \) * 1000;
            \->auditLogger->log(\, \, \, true, null, \);
            return \;
        } catch (\Throwable \) {
            \ = (microtime(true) - \) * 1000;
            \->auditLogger->log(\, \, \, false, \->getMessage(), \);
            throw \;
        }
    }

    private function validateSchema(array \, array \): void
    {
        \ = \['required'] ?? [];
        foreach (\ as \) {
            if (!array_key_exists(\, \)) {
                throw new Exception("Missing required argument: \", 400);
            }
        }
    }
}