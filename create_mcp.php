<?php

$base = __DIR__ . '/app/MCP';
@mkdir($base, 0777, true);
@mkdir($base . '/Contracts', 0777, true);
@mkdir($base . '/Server', 0777, true);
@mkdir($base . '/Protocol', 0777, true);
@mkdir($base . '/Auth', 0777, true);
@mkdir($base . '/Audit', 0777, true);
@mkdir($base . '/Http/Controllers', 0777, true);

file_put_contents($base . '/Contracts/MCPTool.php', <<<EOT
<?php
namespace App\MCP\Contracts;

interface MCPTool
{
    public function name(): string;
    public function description(): string;
    public function inputSchema(): array;
    public function capability(): string;
    public function execute(array \, array \): mixed;
}
EOT);

file_put_contents($base . '/Server/ToolRegistry.php', <<<EOT
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
EOT);

file_put_contents($base . '/Server/ToolDispatcher.php', <<<EOT
<?php
namespace App\MCP\Server;

use App\MCP\Auth\MCPAuthorizer;
use App\MCP\Audit\MCPAuditLogger;
use App\MCP\Contracts\MCPTool;
use Exception;

class ToolDispatcher
{
    public function __construct(
        private ToolRegistry \,
        private MCPAuthorizer \,
        private MCPAuditLogger \
    ) {}

    public function dispatch(string \, array \, array \): mixed
    {
        \ = \->registry->get(\);
        if (!\) {
            throw new Exception("Unknown tool: \", 404); // Using 404 for unknown tool mapping to internal error
        }

        if (!\->authorizer->authorize(\->capability(), \)) {
            \->auditLogger->log(\, \, \, false, "Unauthorized");
            throw new Exception("Unauthorized capability: " . \->capability(), 403);
        }

        \->validateSchema(\->inputSchema(), \);

        \ = microtime(true);
        try {
            \ = \->execute(\, \);
            \ = (microtime(true) - \) * 1000;
            \->auditLogger->log(\, \, \, true, null, \);
            return \;
        } catch (\Throwable \) {
            \ = (microtime(true) - \) * 1000;
            \->auditLogger->log(\, \, \, false, \->getMessage(), \);
            throw \;
        }
    }

    private function validateSchema(array \, array \): void
    {
        \ = \['required'] ?? [];
        foreach (\ as \) {
            if (!array_key_exists(\, \)) {
                throw new Exception("Missing required argument: \", 400);
            }
        }
    }
}
EOT);

file_put_contents($base . '/Protocol/ProtocolNegotiator.php', <<<EOT
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
EOT);

file_put_contents($base . '/Protocol/JsonRpcHandler.php', <<<EOT
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
EOT);

file_put_contents($base . '/Auth/MCPAuthorizer.php', <<<EOT
<?php
namespace App\MCP\Auth;

class MCPAuthorizer
{
    public function authorize(string \, array \): bool
    {
        // \ should contain 'apiKey' which is the authenticated model
        // If the key exists, for now we will grant access. 
        // In a real scenario, we'd check \->capabilities or similar.
        return isset(\['apiKey']);
    }
}
EOT);

file_put_contents($base . '/Audit/MCPAuditLogger.php', <<<EOT
<?php
namespace App\MCP\Audit;

use Modules\Settings\Models\ApiLog;
use App\MCP\Contracts\MCPTool;
use Illuminate\Support\Facades\Log;

class MCPAuditLogger
{
    public function log(MCPTool \, array \, array \, bool \, ?string \ = null, float \ = 0): void
    {
        try {
            if (isset(\['apiKey'])) {
                ApiLog::create([
                    'api_key_id' => \['apiKey']->id,
                    'endpoint' => 'mcp:tool:' . \->name(),
                    'method' => 'MCP',
                    'status_code' => \ ? 200 : 500,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'MCP-Client',
                    // Sanitize arguments by removing passwords/secrets if any exist
                    'payload' => \->sanitize(\),
                    'response' => \ ? ['error' => \] : null,
                    'duration_ms' => \,
                ]);
            }
        } catch (\Throwable \) {
            Log::error("Failed to log MCP audit: " . \->getMessage());
        }
    }

    private function sanitize(array \): array
    {
        \ = \;
        \ = ['password', 'secret', 'token', 'key'];
        foreach (\ as \ => \) {
            foreach (\ as \) {
                if (stripos(\, \) !== false) {
                    \[\] = '***';
                }
            }
        }
        return \;
    }
}
EOT);

file_put_contents($base . '/Http/Controllers/MCPController.php', <<<EOT
<?php
namespace App\MCP\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MCP\Protocol\JsonRpcHandler;
use Modules\Settings\Models\ApiKey;

class MCPController extends Controller
{
    public function __construct(private JsonRpcHandler \) {}

    public function handle(Request \)
    {
        // Extract the JSON payload
        \ = \->json()->all();
        if (empty(\)) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id' => null,
                'error' => ['code' => -32700, 'message' => 'Parse error']
            ], 200);
        }

        // Get authenticated API key (from VerifyApiKey middleware)
        \ = \->header('X-API-KEY');
        \ = ApiKey::where('key', \)->first();
        \ = ['apiKey' => \];

        \ = \->handler->handle(\, \);

        // If it was a notification, response is empty
        if (empty(\)) {
            return response()->noContent();
        }

        return response()->json(\, 200);
    }
}
EOT);

file_put_contents($base . '/MCPServiceProvider.php', <<<EOT
<?php
namespace App\MCP;

use Illuminate\Support\ServiceProvider;
use App\MCP\Server\ToolRegistry;

class MCPServiceProvider extends ServiceProvider
{
    public function register()
    {
        \->app->singleton(ToolRegistry::class, function (\) {
            return new ToolRegistry();
        });
    }

    public function boot()
    {
        // Tools will be registered by their respective modules
    }
}
EOT);

echo "MCP Core Created\n";

