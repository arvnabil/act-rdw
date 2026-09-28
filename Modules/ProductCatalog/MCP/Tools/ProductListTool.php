<?php
namespace Modules\ProductCatalog\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\ProductCatalog\Services\ProductQueryService;

class ProductListTool implements MCPTool
{
    public function __construct(private ProductQueryService $service) {}
    public function name(): string { return 'product.list'; }
    public function description(): string { return 'List products'; }
    public function capability(): string { return 'product.read'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => ['category' => ['type'=>'string'], 'brand' => ['type'=>'string'], 'limit' => ['type'=>'integer']]];
    }
    public function execute(array $arguments, array $context): mixed {
        return $this->service->list($arguments)->toArray();
    }
}