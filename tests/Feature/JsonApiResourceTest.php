<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\ProductCategory;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\NewsTag;
use App\Models\User;

class JsonApiResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run main migrations first
        $this->artisan('migrate');

        // Load all module migrations dynamically via artisan command
        $moduleDirs = glob(base_path('Modules/*/Database/Migrations'));
        foreach ($moduleDirs as $dir) {
            $relativePath = str_replace(base_path() . '/', '', $dir);
            $this->artisan('migrate', ['--path' => $relativePath]);
        }
    }

    /**
     * Test JSON:API serialization of Products index and show endpoints.
     */
    public function test_product_api_returns_valid_json_api_structure()
    {
        // 1. Setup mock data
        $brand = Brand::create([
            'name' => 'Brand ACTiV',
            'slug' => 'brand-activ',
        ]);

        $service = Service::create([
            'name' => 'Logitech Rooms',
            'slug' => 'logitech-rooms',
        ]);

        $category = ProductCategory::create([
            'name' => 'Video Conference',
            'slug' => 'video-conference',
            'is_active' => true,
        ]);

        $solution = ServiceSolution::create([
            'title' => 'Meeting Room Solution',
            'slug' => 'meeting-room-solution',
            'service_id' => $service->id,
        ]);

        $product = Product::create([
            'name' => 'Logitech Meetup 2',
            'slug' => 'logitech-meetup-2',
            'sku' => 'LOGI-MEET2',
            'price' => 15000000.0,
            'description' => 'A camera for huddle rooms.',
            'is_active' => true,
            'is_featured' => true,
            'brand_id' => $brand->id,
            'service_id' => $service->id,
        ]);

        $product->categories()->sync([$category->id]);
        $product->solutions()->sync([$solution->id]);

        // Create SEO metadata
        $product->seo()->create([
            'title' => 'SEO Title Logitech Meetup 2',
            'description' => 'SEO Description Logitech Meetup 2',
            'seoable_id' => $product->id,
            'seoable_type' => Product::class,
        ]);

        // 2. Request GET /api/products
        $responseIndex = $this->getJson('/api/products');

        $responseIndex->assertStatus(200);
        $responseIndex->assertHeader('Content-Type', 'application/vnd.api+json');
        
        // Assert JSON:API structure on Index (Collection)
        $responseIndex->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'attributes' => [
                        'name',
                        'slug',
                        'sku',
                        'price',
                        'description',
                        'image_url',
                        'datasheet_url',
                        'specs',
                        'features',
                        'tags',
                        'specification_text',
                        'features_text',
                        'link_accommerce',
                        'whatsapp_note',
                        'is_active',
                        'is_featured',
                    ],
                    'relationships' => [
                        'brand' => ['data'],
                        'service' => ['data'],
                        'categories' => ['data'],
                        'solutions' => ['data'],
                        'seo' => ['data'],
                    ]
                ]
            ],
            'included',
            'links',
            'meta',
        ]);

        // Assert relationships link correctly in "included" block
        $responseIndex->assertJsonFragment([
            'type' => 'brands',
            'id' => (string) $brand->id,
            'attributes' => [
                'name' => 'Brand ACTiV',
                'slug' => 'brand-activ',
            ]
        ]);

        $responseIndex->assertJsonFragment([
            'type' => 'categories',
            'id' => (string) $category->id,
            'attributes' => [
                'name' => 'Video Conference',
                'slug' => 'video-conference',
            ]
        ]);

        // 3. Request GET /api/products/{slug}
        $responseShow = $this->getJson('/api/products/logitech-meetup-2');

        $responseShow->assertStatus(200);
        $responseShow->assertHeader('Content-Type', 'application/vnd.api+json');

        // Assert JSON:API structure on Show (Single Resource)
        $responseShow->assertJsonStructure([
            'data' => [
                'id',
                'type',
                'attributes',
                'relationships',
            ],
            'included',
        ]);

        $this->assertEquals((string) $product->id, $responseShow->json('data.id'));
        $this->assertEquals('products', $responseShow->json('data.type'));
    }

    /**
     * Test JSON:API serialization of News index and show endpoints.
     */
    public function test_news_api_returns_valid_json_api_structure()
    {
        // 1. Setup mock data
        $author = User::create([
            'name' => 'ACTiV Writer',
            'email' => 'writer@active.co.id',
            'password' => bcrypt('secret-password'),
        ]);

        $category = NewsCategory::create([
            'name' => 'Technology News',
            'slug' => 'technology-news',
        ]);

        $tag = NewsTag::create([
            'name' => 'AI',
            'slug' => 'ai',
        ]);

        $news = News::create([
            'title' => 'The Future of Video Conferencing',
            'slug' => 'the-future-of-video-conferencing',
            'excerpt' => 'AI is shaping meetings.',
            'content' => '<p>Meeting rooms are getting smarter with Gemini AI integration.</p>',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $author->id,
        ]);

        $news->categories()->sync([$category->id]);
        $news->tags()->sync([$tag->id]);

        $news->seo()->create([
            'title' => 'SEO News Title',
            'description' => 'SEO News Description',
            'seoable_id' => $news->id,
            'seoable_type' => News::class,
        ]);

        // 2. Request GET /api/news
        $responseIndex = $this->getJson('/api/news');

        $responseIndex->assertStatus(200);
        $responseIndex->assertHeader('Content-Type', 'application/vnd.api+json');

        // Assert JSON:API structure on Index (Collection)
        $responseIndex->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'attributes' => [
                        'title',
                        'slug',
                        'excerpt',
                        'content',
                        'status',
                        'published_at',
                        'thumbnail_url',
                    ],
                    'relationships' => [
                        'categories' => ['data'],
                        'tags' => ['data'],
                        'author' => ['data'],
                        'seo' => ['data'],
                    ]
                ]
            ],
            'included',
        ]);

        // Assert relationships resolved correctly
        $responseIndex->assertJsonFragment([
            'type' => 'news',
            'id' => (string) $news->id,
        ]);

        $responseIndex->assertJsonFragment([
            'type' => 'categories',
            'id' => (string) $category->id,
            'attributes' => [
                'name' => 'Technology News',
                'slug' => 'technology-news',
            ]
        ]);

        $responseIndex->assertJsonFragment([
            'type' => 'tags',
            'id' => (string) $tag->id,
            'attributes' => [
                'name' => 'AI',
                'slug' => 'ai',
            ]
        ]);

        // 3. Request GET /api/news/{slug}
        $responseShow = $this->getJson('/api/news/the-future-of-video-conferencing');

        $responseShow->assertStatus(200);
        $responseShow->assertHeader('Content-Type', 'application/vnd.api+json');
        
        $this->assertEquals((string) $news->id, $responseShow->json('data.id'));
        $this->assertEquals('news', $responseShow->json('data.type'));
    }
}
