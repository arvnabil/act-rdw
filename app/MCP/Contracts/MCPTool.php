<?php

namespace App\MCP\Contracts;

interface MCPTool
{
    public function name(): string;
    public function description(): string;
    public function inputSchema(): array;
    public function capability(): string;
    public function execute(array $arguments, array $context): mixed;
}