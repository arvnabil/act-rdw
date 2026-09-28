<?php

namespace App\MCP\Server;

use App\MCP\Contracts\MCPTool;
use Exception;

class ToolRegistry
{
    private array $tools = [];

    public function register(MCPTool $tool): void
    {
        if (isset($this->tools[$tool->name()])) {
            throw new Exception("Tool already registered: " . $tool->name());
        }
        $this->tools[$tool->name()] = $tool;
    }

    public function get(string $name): ?MCPTool
    {
        return $this->tools[$name] ?? null;
    }

    public function all(): array
    {
        return array_values($this->tools);
    }
}