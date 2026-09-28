<?php
namespace App\MCP;

use Illuminate\Support\ServiceProvider;
use App\MCP\Server\ToolRegistry;

class MCPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ToolRegistry::class, function ($app) {
            $registry = new ToolRegistry();
            // Register Product Tools
            $registry->register($app->make(\Modules\ProductCatalog\MCP\Tools\ProductListTool::class));
            $registry->register($app->make(\Modules\ProductCatalog\MCP\Tools\ProductGetTool::class));
            $registry->register($app->make(\Modules\ProductCatalog\MCP\Tools\ProductUpsertTool::class));
            $registry->register($app->make(\Modules\ProductCatalog\MCP\Tools\ProductDeleteTool::class));
            
            // Register News Tools
            $registry->register($app->make(\Modules\News\MCP\Tools\NewsListTool::class));
            $registry->register($app->make(\Modules\News\MCP\Tools\NewsGetTool::class));
            $registry->register($app->make(\Modules\News\MCP\Tools\NewsUpsertTool::class));
            $registry->register($app->make(\Modules\News\MCP\Tools\NewsDeleteTool::class));
            
            return $registry;
        });
    }

    public function boot()
    {
    }
}