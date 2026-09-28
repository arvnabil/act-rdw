<?php
namespace Modules\News\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\News\Services\NewsQueryService;

class NewsListTool implements MCPTool
{
    public function __construct(private NewsQueryService $service) {}
    public function name(): string { return 'news.list'; }
    public function description(): string { return 'List news'; }
    public function capability(): string { return 'news.read'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => ['category' => ['type'=>'string'], 'tag' => ['type'=>'string'], 'limit' => ['type'=>'integer']]];
    }
    public function execute(array $arguments, array $context): mixed {
        return $this->service->list($arguments)->toArray();
    }
}