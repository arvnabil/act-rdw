<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\ProductCategory;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\NewsTag;
use App\Models\User;

class RestApiComprehensiveRegressionTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_products_index_endpoint()
    {
        $brand = Brand::create(['name' => 'Brand A', 'slug' => 'brand-a']);
        $service = Service::create(['name' => 'Service A', 'slug' => 'service-a']);
        $category = ProductCategory::create(['name' => 'Cat A', 'slug' => 'cat-a', 'is_active' => true]);

        $activeProduct = Product::create([
            'name' => 'Active Product',
            'slug' => 'active-product',
            'brand_id' => $brand->id,
            'service_id' => $service->id,
            'is_active' => true,
        ]);
        $activeProduct->categories()->sync([$category->id]);

        $inactiveProduct = Product::create([
            'name' => 'Inactive Product',
            'slug' => 'inactive-product',
            'brand_id' => $brand->id,
            'service_id' => $service->id,
           'is_active' => false,
        ]);

        // 1. Basic index (only active)
        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.attributes.name', 'Active Product');
        $response->assertJsonCount(1, 'data');

        // 2. Filter by category
        $responseCat = $this->getJson('/api/products?category=cat-a');
        $responseCat->assertStatus(200);
        $responseCat->assertJsonCount(1, 'data');

        $responseCatEmpty = $this->getJson('/api/products?category=non-existent');
        $responseCatEmpty->assertStatus(200);
        $responseCatEmpty->assertJsonCount(0, 'data');

        // 3. Filter by brand
        $responseBrand = $this->getJson('/api/products?brand=brand-a');
        $responseBrand->assertStatus(200);
        $responseBrand->assertJsonCount(1, 'data');
    }

    public function test_products_show_endpoint()
    {
        $brand = Brand::create(['name' => 'Yealink', 'slug' => 'yealink']);
        $service = Service::create(['name' => 'Video Conferencing', 'slug' => 'video-conferencing']);
        $product = Product::create([
           'name' => 'MeetingBoard 65',
            'slug' => 'meetingboard-65',
            'brand_id' => $brand->id,
            'service_id' => $service->id,
            'is_active' => true,
        ]);

        // Success
        $response = $this->getJson('/api/products/meetingboard-65');
        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.name', 'MeetingBoard 65');
        $response->assertJsonPath('data.type', 'products');

        // 404
        $notFound = $this->getJson('/api/products/does-not-exist');
        $notFound->assertStatus(404);
        $notFound->assertJson(['message' => 'Product not found']);
    }

    public function test_products_import_endpoint_unauthenticated()
    {
        $response = $this->postJson('/api/products/import', [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'brand_name' => 'Test Brand',
            'service_name' => 'Test Service',
        ]);

        $response->assertStatus(401);
    }

    public function test_products_import_endpoint_validation_errors()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/products/import', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'slug', 'brand_name', 'service_name']);
    }

    public function test_products_import_endpoint_successful_upsert()
    {
        $user = User::factory()->create();

        $collabService = Service::create(['name' => 'Collaboration', 'slug' => 'collaboration-svc']);
        $solution = ServiceSolution::create(['title' => 'Unified Communications', 'slug' => 'unified-communications', 'service_id' => $collabService->id]);

        $payload = [
            'name' => 'Smart Hub Pro',
            'slug' => 'smart-hub-pro',
            'sku' => 'SHP-001',
            'price' => 12500000,
            'description' => 'Enterprise conference hub',
            'brand_name' => 'Logitech Enterprise',
            'service_name' => 'Collaboration Rooms',
            'category_name' => ['Video Hardware', 'Smart Office'],
            'solutions' => ['Unified Communications'],
            'specs' => json_encode(['ports' => '4x HDMI', 'power' => '65W']),
            'features' => json_encode(['AI Framing', 'Noise Cancellation']),
            'tags' => 'hardware, room-kit',
            'seo_title' => 'Buy Smart Hub Pro Online',
            'seo_description' => 'Best enterprise hub for your meeting rooms.',
            'seo_keywords' => 'smart hub, logitech, conference',
        ];

        // 1. First import creates
        $response = $this->actingAs($user, 'api')->postJson('/api/products/import', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Product imported successfully');
        $response->assertJsonPath('product.data.attributes.name', 'Smart Hub Pro');
        $response->assertJsonPath('product.data.attributes.sku', 'SHP-001');

        $this->assertDatabaseHas('products', [
            'slug' => 'smart-hub-pro',
            'name' => 'Smart Hub Pro',
        ]);
        $this->assertDatabaseHas('brands', [
            'name' => 'Logitech Enterprise',
            'slug' => 'logitech-enterprise',
        ]);
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Video Hardware',
            'slug' => 'video-hardware',
        ]);

        // 2. Second import updates existing
        $updatePayload = array_merge($payload, [
            'name' => 'Smart Hub Pro Gen 2',
            'price' => 14000000,
        ]);

        $updateResponse = $this->actingAs($user, 'api')->postJson('/api/products/import', $updatePayload);
        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonPath('product.data.attributes.name', 'Smart Hub Pro Gen 2');

        $this->assertDatabaseHas('products', [
            'slug' => 'smart-hub-pro',
            'name' => 'Smart Hub Pro Gen 2',
            'price' => 14000000,
        ]);
    }

    public function test_news_index_endpoint()
    {
        $category = NewsCategory::create(['name' => 'Tech', 'slug' => 'tech']);
        $tag = NewsTag::create(['name' => 'AI', 'slug' => 'ai']);

        $published = News::create([
            'title' => 'Published Article',
            'slug' => 'published-article',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $published->categories()->sync([$category->id]);
        $published->tags()->sync([$tag->id]);

        $draft = News::create([
            'title' => 'Draft Article',
            'slug' => 'draft-article',
            'status' => 'draft',
        ]);

        // 1. Basic index (only published)
        $response = $this->getJson('/api/news');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.attributes.title', 'Published Article');
        $response->assertJsonCount(1, 'data');

        // 2. Filter by category
        $responseCat = $this->getJson('/api/news?category=tech');
        $responseCat->assertStatus(200);
        $responseCat->assertJsonCount(1, 'data');

        // 3. Filter by tag
        $responseTag = $this->getJson('/api/news?tag=ai');
        $responseTag->assertStatus(200);
        $responseTag->assertJsonCount(1, 'data');
    }

    public function test_news_show_endpoint()
    {
        $news = News::create([
            'title' => 'Breaking News',
            'slug' => 'breaking-news',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Success
        $response = $this->getJson('/api/news/breaking-news');
        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.title', 'Breaking News');
        $response->assertJsonPath('data.type', 'news');

        // 404
        $notFound = $this->getJson('/api/news/does-not-exist');
        $notFound->assertStatus(404);
        $notFound->assertJson(['message' => 'News not found']);
    }

    public function test_news_import_endpoint_unauthenticated()
    {
        $response = $this->postJson('/api/news/import', [
            'title' => 'Test Article',
            'slug' => 'test-article',
        ]);

        $response->assertStatus(401);
    }

    public function test_news_import_endpoint_validation_errors()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/news/import', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'slug']);
    }

    public function test_news_import_endpoint_successful_upsert()
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'New Release Announced',
            'slug' => 'new-release-announced',
            'excerpt' => 'Short summary of the release.',
            'content' => '<p>Full content of the announcement.</p>',
            'status' => 'published',
            'published_at' => '2026-09-28 10:00:00',
            'categories' => ['Company News', 'Product Updates'],
            'tags' => ['update', 'v2'],
            'seo_title' => 'New Release Announcement SEO',
            'seo_description' => 'Read our latest update.',
            'seo_keywords' => 'release, update, news',
        ];

        // 1. Create via import
        $response = $this->actingAs($user, 'api')->postJson('/api/news/import', $payload);
        $response->assertStatus(200);
        $response->assertJsonPath('message', 'News imported successfully');
        $response->assertJsonPath('news.data.attributes.title', 'New Release Announced');

        $this->assertDatabaseHas('news', [
            'slug' => 'new-release-announced',
            'title' => 'New Release Announced',
            'status' => 'published',
        ]);
        $this->assertDatabaseHas('news_categories', [
            'name' => 'Company News',
            'slug' => 'company-news',
        ]);
        $this->assertDatabaseHas('news_tags', [
            'name' => 'update',
            'slug' => 'update',
        ]);

        // 2. Update via import
        $updatePayload = array_merge($payload, [
            'title' => 'New Release Announced v2',
        ]);

        $updateResponse = $this->actingAs($user, 'api')->postJson('/api/news/import', $updatePayload);
        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonPath('news.data.attributes.title', 'New Release Announced v2');

        $this->assertDatabaseHas('news', [
            'slug' => 'new-release-announced',
            'title' => 'New Release Announced v2',
        ]);
    }
}
