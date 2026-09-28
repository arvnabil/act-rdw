<?php
namespace Modules\ProductCatalog\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\ProductCatalog\Services\ProductMutationService;
use Exception;

class ProductDeleteTool implements MCPTool
{
    public function __construct(private ProductMutationService $service) {}
    public function name(): string { return 'product.delete'; }
    public function description(): string { return 'Delete a product'; }
    public function capability(): string { return 'product.delete'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'required' => ['slug'], 'properties' => ['slug' => ['type'=>'string']]];
    }
    public function execute(array $arguments, array $context): mixed {
        $deleted = $this->service->delete($arguments['slug']);
        if (!$deleted) throw new Exception("Product not found", 404);
        return ['success' => true];
    }
}