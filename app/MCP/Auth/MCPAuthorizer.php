<?php
namespace App\MCP\Auth;

class MCPAuthorizer
{
    public function authorize(string $capability, array $context): bool
    {
        $apiKey = $context['apiKey'] ?? null;
        if (!$apiKey) return false;
        $caps = $apiKey->capabilities ?? [];
        return in_array($capability, $caps) || in_array('*', $caps);
    }
}