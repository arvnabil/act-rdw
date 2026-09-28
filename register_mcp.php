<?php

file_put_contents('routes/api.php', <<<EOT

// MCP Routes
use App\MCP\Http\Controllers\MCPController;
Route::group(['middleware' => [\App\Http\Middleware\VerifyApiKey::class]], function () {
    Route::post('/mcp', [MCPController::class, 'handle']);
});
EOT
, FILE_APPEND);

\ = file_get_contents('bootstrap/providers.php');
\ = str_replace(
    "App\Providers\AppServiceProvider::class,",
    "App\Providers\AppServiceProvider::class,\n    App\MCP\MCPServiceProvider::class,",
    \
);
file_put_contents('bootstrap/providers.php', \);

echo "Routes and provider registered\n";
