<?php
namespace App\MCP\Protocol;

class ProtocolNegotiator
{
    private array \ = [
        '2024-11-05',
        '2024-10-01'
    ];

    public function negotiate(string \): string
    {
        if (in_array(\, \->supportedVersions)) {
            return \;
        }
        return \->supportedVersions[0];
    }
}