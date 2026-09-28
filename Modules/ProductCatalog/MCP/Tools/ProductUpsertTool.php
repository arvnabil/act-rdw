<?php
namespace Modules\ProductCatalog\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\ProductCatalog\Services\ProductMutationService;

class ProductUpsertTool implements MCPTool
{
    public function __construct(private ProductMutationService $service) {}
    public function name(): string { return 'product.upsert'; }
    public function description(): string { return 'Upsert a product'; }
    public function capability(): string { return 'product.write'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'required' => ['slug', 'name'], 'properties' => ['slug' => ['type'=>'string'], 'name' => ['type'=>'string']]];
    }
    public function execute(array $arguments, array $context): mixed {
        return $this->service->upsert($arguments)->toArray();
    }
}