<?php
namespace App\MCP\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MCP\Protocol\JsonRpcHandler;

class MCPController extends Controller
{
    public function __construct(private JsonRpcHandler $handler) {}

    public function handle(Request $request)
    {
        $payload = $request->json()->all();
        if (empty($payload)) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id' => null,
                'error' => ['code' => -32700, 'message' => 'Parse error']
            ], 200);
        }

        $apiKey = $request->attributes->get('apiKey');
        $context = ['apiKey' => $apiKey];

        $response = $this->handler->handle($payload, $context);

        if (empty($response)) {
            return response()->noContent();
        }

        return response()->json($response, 200);
    }
}