<?php
namespace Modules\News\MCP\Tools;
use App\MCP\Contracts\MCPTool;
use Modules\News\Services\NewsMutationService;
use Exception;

class NewsDeleteTool implements MCPTool
{
    public function __construct(private NewsMutationService $service) {}
    public function name(): string { return 'news.delete'; }
    public function description(): string { return 'Delete a news article'; }
    public function capability(): string { return 'news.delete'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'required' => ['slug'], 'properties' => ['slug' => ['type'=>'string']]];
    }
    public function execute(array $arguments, array $context): mixed {
        $deleted = $this->service->delete($arguments['slug']);
        if (!$deleted) throw new Exception("News not found", 404);
        return ['success' => true];
    }
}