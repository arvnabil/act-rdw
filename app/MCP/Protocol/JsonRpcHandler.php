<?php

namespace App\MCP\Protocol;

use App\MCP\Server\ToolRegistry;
use App\MCP\Server\ToolDispatcher;
use Throwable;

class JsonRpcHandler
{
    public function __construct(
        private ProtocolNegotiator $negotiator,
        private ToolRegistry $registry,
        private ToolDispatcher $dispatcher
    ) {}

    public function handle(array $payload, array $context): array
    {
        $id = $payload['id'] ?? null;
        $method = $payload['method'] ?? null;
        $params = $payload['params'] ?? [];

        if (!$method) {
            return $this->error($id, -32600, "Invalid Request");
        }

        try {
            switch ($method) {
                case 'initialize':
                    $clientVersion = $params['protocolVersion'] ?? '';
                    $negotiatedVersion = $this->negotiator->negotiate($clientVersion);
                    return $this->success($id, [
                        'protocolVersion' => $negotiatedVersion,
                        'capabilities' => ['tools' => []],
                        'serverInfo' => ['name' => 'LaravelMCP', 'version' => '1.0.0']
                    ]);
                
                case 'notifications/initialized':
                    return []; // No response for notifications
                
                case 'tools/list':
                    $tools = array_map(function($tool) {
                        return [
                            'name' => $tool->name(),
                            'description' => $tool->description(),
                            'inputSchema' => $tool->inputSchema()
                        ];
                    }, $this->registry->all());
                    return $this->success($id, ['tools' => $tools]);
                
                case 'tools/call':
                    $toolName = $params['name'] ?? '';
                    $arguments = $params['arguments'] ?? [];
                    $result = $this->dispatcher->dispatch($toolName, $arguments, $context);
                    // Return result wrapped in text content
                    return $this->success($id, [
                        'content' => [
                            ['type' => 'text', 'text' => is_string($result) ? $result : json_encode($result)]
                        ]
                    ]);
                
                default:
                    return $this->error($id, -32601, "Method not found: {$method}");
            }
        } catch (Throwable $e) {
            $code = $e->getCode();
            if ($code == 404) {
                $rpcCode = -32601;
            } elseif ($code == 400) {
                $rpcCode = -32602;
            } elseif ($code == 403) {
                $rpcCode = -32003;
            } else {
                $rpcCode = -32000;
            }
            return $this->error($id, $rpcCode, $e->getMessage());
        }
    }

    private function success($id, $result): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result
        ];
    }

    private function error($id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ];
    }
}