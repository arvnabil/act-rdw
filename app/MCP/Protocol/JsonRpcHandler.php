<?php
namespace App\MCP\Protocol;

use App\MCP\Server\ToolRegistry;
use App\MCP\Server\ToolDispatcher;

class JsonRpcHandler
{
    public function __construct(
        private ProtocolNegotiator \,
        private ToolRegistry \,
        private ToolDispatcher \
    ) {}

    public function handle(array \, array \): array
    {
        \ = \['id'] ?? null;
        \ = \['method'] ?? null;
        \ = \['params'] ?? [];

        if (!\) {
            return \->error(\, -32600, "Invalid Request");
        }

        try {
            switch (\) {
                case 'initialize':
                    \ = \['protocolVersion'] ?? '';
                    \ = \->negotiator->negotiate(\);
                    return \->success(\, [
                        'protocolVersion' => \,
                        'capabilities' => ['tools' => []],
                        'serverInfo' => ['name' => 'LaravelMCP', 'version' => '1.0.0']
                    ]);
                
                case 'notifications/initialized':
                    return []; // No response for notifications
                
                case 'tools/list':
                    \ = array_map(function(\) {
                        return [
                            'name' => \->name(),
                            'description' => \->description(),
                            'inputSchema' => \->inputSchema()
                        ];
                    }, \->registry->all());
                    return \->success(\, ['tools' => \]);
                
                case 'tools/call':
                    \ = \['name'] ?? '';
                    \ = \['arguments'] ?? [];
                    \ = \->dispatcher->dispatch(\, \, \);
                    // Return result wrapped in text content
                    return \->success(\, [
                        'content' => [
                            ['type' => 'text', 'text' => is_string(\) ? \ : json_encode(\)]
                        ]
                    ]);
                
                default:
                    return \->error(\, -32601, "Method not found: \");
            }
        } catch (\Throwable \) {
            \ = \->getCode();
            if (\ == 404) \ = -32601;
            elseif (\ == 400) \ = -32602;
            else \ = -32000;
            return \->error(\, \, \->getMessage());
        }
    }

    private function success(\, \): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => \,
            'result' => \
        ];
    }

    private function error(\, \, \): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => \,
            'error' => [
                'code' => \,
                'message' => \
            ]
        ];
    }
}