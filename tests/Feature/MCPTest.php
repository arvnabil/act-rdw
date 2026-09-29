<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Settings\Models\ApiKey;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\ProductCategory;
use Modules\Services\Models\Service;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\NewsTag;

class MCPTest extends TestCase
{
    use RefreshDatabase;

    protected ApiKey $adminApiKey;
    protected ApiKey $readOnlyApiKey;

    protected function setUp(): void
    {
        parent::setUp();

        // Run main migrations
        $this->artisan('migrate');

        // Load all module migrations dynamically
        $moduleDirs = glob(base_path('Modules/*/Database/Migrations'));
        foreach ($moduleDirs as $dir) {
            $relativePath = str_replace(base_path() . '/', '', $dir);
            $this->artisan('migrate', ['--path' => $relativePath]);
        }

        $this->adminApiKey = ApiKey::create([
            'name' => 'Admin MCP Key',
            'key' => 'mcp-admin-key-secret-12345',
            'is_active' => true,
            'capabilities' => ['*'],
        ]);

        $this->readOnlyApiKey = ApiKey::create([
            'name' => 'ReadOnly MCP Key',
            'key' => 'mcp-readonly-key-secret-12345',
            'is_active' => true,
            'capabilities' => ['product.read', 'news.read'],
        ]);
    }

    public function test_mcp_unauthenticated_request_rejected()
    {
        $response = $this->postJson('/api/mcp', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => ['protocolVersion' => '2024-11-05']
        ]);

        $response->assertStatus(401);
    }

    public function test_mcp_initialize()
    {
        $response = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'initialize',
                'params' => ['protocolVersion' => '2024-11-05']
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jsonrpc', 'id',
                'result' => ['protocolVersion', 'capabilities', 'serverInfo'],
            ])
            ->assertJson([
                'jsonrpc' => '2.0',
                'id' => 1,
                'result' => [
                    'protocolVersion' => '2024-11-05',
                    'serverInfo' => [
                        'name' => 'LaravelMCP',
                        'version' => '1.0.0',
                    ],
                ],
            ]);
    }

    public function test_mcp_initialize_capabilities_tools_is_json_object()
    {
        $response = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'initialize',
                'params' => ['protocolVersion' => '2024-11-05'],
            ]);

        $response->assertStatus(200);

        // Decode raw JSON preserving object/array distinction (assoc=false)
        $raw = json_decode($response->getContent(), false);

        $this->assertObjectHasProperty('result', $raw, 'Response must have result');
        $this->assertObjectHasProperty('protocolVersion', $raw->result, 'result must have protocolVersion');
        $this->assertObjectHasProperty('serverInfo', $raw->result, 'result must have serverInfo');
        $this->assertObjectHasProperty('capabilities', $raw->result, 'result must have capabilities');
        $this->assertObjectHasProperty('tools', $raw->result->capabilities, 'capabilities must have tools');

        // Critical: tools must be a JSON object {}, not an array
        $this->assertInstanceOf(
            \stdClass::class,
            $raw->result->capabilities->tools,
            'capabilities.tools must serialize as JSON object {}, not array'
        );
    }

    public function test_mcp_tools_list()
    {
        $response = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 2,
                'method' => 'tools/list',
                'params' => []
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jsonrpc',
                'id',
                'result' => [
                    'tools' => [
                        '*' => ['name', 'description', 'inputSchema']
                    ]
                ]
            ]);

        $tools = collect($response->json('result.tools'))->pluck('name')->toArray();
        $this->assertContains('product.list', $tools);
        $this->assertContains('product.get', $tools);
        $this->assertContains('product.upsert', $tools);
        $this->assertContains('product.delete', $tools);
        $this->assertContains('news.list', $tools);
        $this->assertContains('news.get', $tools);
        $this->assertContains('news.upsert', $tools);
        $this->assertContains('news.delete', $tools);
    }

    public function test_mcp_products_list_tool()
    {
        $brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $service = Service::create(['name' => 'Test Service', 'slug' => 'test-service']);
        Product::create([
            'name' => 'Awesome Product',
            'slug' => 'awesome-product',
            'brand_id' => $brand->id,
            'service_id' => $service->id,
            'is_active' => true,
        ]);

        $response = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 3,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'product.list',
                    'arguments' => ['limit' => 10]
                ]
            ]);

        $response->assertStatus(200);
        $content = $response->json('result.content.0.text');
        $this->assertNotNull($content);
        $this->assertStringContainsString('Awesome Product', $content);
    }

    public function test_mcp_product_get_tool()
    {
        $brand = Brand::create(['name' => 'Brand Get', 'slug' => 'brand-get']);
        $service = Service::create(['name' => 'Service Get', 'slug' => 'service-get']);
        Product::create([
            'name' => 'Get Me Product',
            'slug' => 'get-me-product',
            'brand_id' => $brand->id,
            'service_id' => $service->id,
            'is_active' => true,
        ]);

        $response = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 4,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'product.get',
                    'arguments' => ['slug' => 'get-me-product']
                ]
            ]);

        $response->assertStatus(200);
        $content = $response->json('result.content.0.text');
        $this->assertStringContainsString('Get Me Product', $content);
    }

    public function test_mcp_capabilities_enforcement()
    {
        // readOnly key should be rejected from calling product.delete
        $response = $this->withHeaders(['X-API-KEY' => $this->readOnlyApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 5,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'product.delete',
                    'arguments' => ['slug' => 'any-product']
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'jsonrpc' => '2.0',
                'id' => 5,
                'error' => [
                    'code' => -32003,
                ]
            ]);
    }

    public function test_mcp_product_delete_tool()
    {
        $brand = Brand::create(['name' => 'Brand Del', 'slug' => 'brand-del']);
        $service = Service::create(['name' => 'Service Del', 'slug' => 'service-del']);
        $product = Product::create([
            'name' => 'Delete Me Product',
            'slug' => 'delete-me-product',
            'brand_id' => $brand->id,
            'service_id' => $service->id,
            'is_active' => true,
        ]);

        $response = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 6,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'product.delete',
                    'arguments' => ['slug' => 'delete-me-product']
                ]
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_mcp_news_tools()
    {
        $category = NewsCategory::create(['name' => 'Tech', 'slug' => 'tech', 'is_active' => true]);
        $news = News::create([
            'title' => 'AI Launch Today',
            'slug' => 'ai-launch-today',
            'content' => 'Full news article content',
            'category_id' => $category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        // 1. news.list
        $resList = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 10,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'news.list',
                    'arguments' => ['limit' => 5]
                ]
            ]);
        $resList->assertStatus(200);
        $this->assertStringContainsString('AI Launch Today', $resList->json('result.content.0.text'));

        // 2. news.get
        $resGet = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 11,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'news.get',
                    'arguments' => ['slug' => 'ai-launch-today']
                ]
            ]);
        $resGet->assertStatus(200);
        $this->assertStringContainsString('AI Launch Today', $resGet->json('result.content.0.text'));

        // 3. news.delete
        $resDel = $this->withHeaders(['X-API-KEY' => $this->adminApiKey->key])
            ->postJson('/api/mcp', [
                'jsonrpc' => '2.0',
                'id' => 12,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'news.delete',
                    'arguments' => ['slug' => 'ai-launch-today']
                ]
            ]);
        $resDel->assertStatus(200);
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }
}