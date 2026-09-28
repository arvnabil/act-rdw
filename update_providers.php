<?php
$path = 'bootstrap/providers.php';
$content = file_get_contents($path);
$content = str_replace(
    'App\Providers\AppServiceProvider::class,',
    "App\Providers\AppServiceProvider::class,\n    App\MCP\MCPServiceProvider::class,",
    $content
);
file_put_contents($path, $content);