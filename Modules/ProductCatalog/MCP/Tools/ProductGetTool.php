<?php
namespace Modules\ProductCatalog\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\ProductCatalog\Services\ProductQueryService;
use Exception;

class ProductGetTool implements MCPTool
{
    public function __construct(private ProductQueryService $service) {}
    public function name(): string { return 'product.get'; }
    public function description(): string { return 'Get a product by slug'; }
    public function capability(): string { return 'product.read'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'required' => ['slug'], 'properties' => ['slug' => ['type'=>'string']]];
    }
    public function execute(array $arguments, array $context): mixed {
        $res = $this->service->findBySlug($arguments['slug']);
        if (!$res) throw new Exception("Product not found", 404);
        return $res->toArray();
    }
}