<?php

namespace App\MCP\Server;

use App\MCP\Auth\MCPAuthorizer;
use App\MCP\Audit\MCPAuditLogger;
use App\MCP\Contracts\MCPTool;
use Exception;
use Throwable;

class ToolDispatcher
{
    public function __construct(
        private ToolRegistry $registry,
        private MCPAuthorizer $authorizer,
        private MCPAuditLogger $auditLogger
    ) {}

    public function dispatch(string $name, array $arguments, array $context): mixed
    {
        $tool = $this->registry->get($name);
        if (!$tool) {
            throw new Exception("Unknown tool: {$name}", 404);
        }

        if (!$this->authorizer->authorize($tool->capability(), $context)) {
            $this->auditLogger->log($tool, $arguments, $context, false, "Unauthorized");
            throw new Exception("Unauthorized capability: " . $tool->capability(), 403);
        }

        $this->validateSchema($tool->inputSchema(), $arguments);

        $startTime = microtime(true);
        try {
            $result = $tool->execute($arguments, $context);
            $duration = (microtime(true) - $startTime) * 1000;
            $this->auditLogger->log($tool, $arguments, $context, true, null, $duration);
            return $result;
        } catch (Throwable $e) {
            $duration = (microtime(true) - $startTime) * 1000;
            $this->auditLogger->log($tool, $arguments, $context, false, $e->getMessage(), $duration);
            throw $e;
        }
    }

    private function validateSchema(array $schema, array $arguments): void
    {
        $required = $schema['required'] ?? [];
        foreach ($required as $field) {
            if (!array_key_exists($field, $arguments)) {
                throw new Exception("Missing required argument: {$field}", 400);
            }
        }
    }
}