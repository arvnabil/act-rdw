<?php
namespace App\MCP\Server;
use App\MCP\Contracts\MCPTool;

class ToolRegistry
{
    private array \ = [];

    public function register(MCPTool \): void
    {
        if (isset(\->tools[\->name()])) {
            throw new \Exception("Tool already registered: " . \->name());
        }
        \->tools[\->name()] = \;
    }

    public function get(string \): ?MCPTool
    {
        return \->tools[\] ?? null;
    }

    public function all(): array
    {
        return array_values(\->tools);
    }
}