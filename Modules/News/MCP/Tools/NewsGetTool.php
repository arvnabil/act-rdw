<?php
namespace Modules\News\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\News\Services\NewsQueryService;
use Exception;

class NewsGetTool implements MCPTool
{
    public function __construct(private NewsQueryService $service) {}
    public function name(): string { return 'news.get'; }
    public function description(): string { return 'Get a news article by slug'; }
    public function capability(): string { return 'news.read'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'required' => ['slug'], 'properties' => ['slug' => ['type'=>'string']]];
    }
    public function execute(array $arguments, array $context): mixed {
        $res = $this->service->findBySlug($arguments['slug']);
        if (!$res) throw new Exception("News not found", 404);
        return $res->toArray();
    }
}