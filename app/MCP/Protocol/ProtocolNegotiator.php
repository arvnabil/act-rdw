<?php

namespace App\MCP\Protocol;

class ProtocolNegotiator
{
    private array $supportedVersions = [
        '2024-11-05',
        '2024-10-01',
    ];

    public function negotiate(string $clientVersion): string
    {
        if (in_array($clientVersion, $this->supportedVersions)) {
            return $clientVersion;
        }
        return $this->supportedVersions[0];
    }
}