<?php
namespace Modules\News\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\News\Services\NewsMutationService;

class NewsUpsertTool implements MCPTool
{
    public function __construct(private NewsMutationService $service) {}
    public function name(): string { return 'news.upsert'; }
    public function description(): string { return 'Upsert a news article'; }
    public function capability(): string { return 'news.write'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'required' => ['slug', 'title'], 'properties' => ['slug' => ['type'=>'string'], 'title' => ['type'=>'string']]];
    }
    public function execute(array $arguments, array $context): mixed {
        return $this->service->upsert($arguments)->toArray();
    }
}