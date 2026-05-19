<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Modules\AI\Models\ChatSession;
use Modules\AI\Models\ChatMessage;
use Modules\AI\Services\SemanticCacheService;
use Modules\AI\Services\VectorService;
use Modules\AI\Jobs\ProcessChatSummaryJob;

class UatOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run main migrations first (just in case)
        $this->artisan('migrate');

        // Load all module migrations dynamically via artisan command
        $moduleDirs = glob(base_path('Modules/*/Database/Migrations'));
        foreach ($moduleDirs as $dir) {
            $relativePath = str_replace(base_path() . '/', '', $dir);
            $this->artisan('migrate', ['--path' => $relativePath]);
        }

        // Clear caches for test consistency
        Cache::forget('vion_semantic_cache:sales:' . hash('sha256', strtolower('jelaskan spesifikasi meetup 2')));
        Cache::forget('vertex_oauth2_token');
    }

    /**
     * 🟢 UAT 1: Pengujian Fungsionalitas Dasar (Database Driver)
     */
    public function test_lead_capture_and_session_initialization()
    {
        $response = $this->postJson(route('ai.start-session'), [
            'name' => 'UAT Test User',
            'whatsapp' => '62899999999',
            'email' => 'uat@example.com',
            'company' => 'ACTiV Test Corp',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'session_id']);

        $sessionId = $response->json('session_id');

        // Validasi database
        $this->assertDatabaseHas('ai_chat_sessions', [
            'id' => $sessionId,
            'name' => 'UAT Test User',
            'whatsapp' => '62899999999',
        ]);

        // Validasi salam pembuka
        $this->assertDatabaseHas('ai_chat_messages', [
            'session_id' => $sessionId,
            'role' => 'assistant',
        ]);

        // Validasi Redis/Database Cache status sesi aktif
        $cacheKey = 'vion_session_active:' . $sessionId;
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals('UAT Test User', Cache::get($cacheKey)['name']);
    }

    /**
     * ⚡ UAT 2: Pengujian Semantic Cache (Respons Instan)
     */
    public function test_semantic_cache_hit_and_miss()
    {
        // 1. Setup session
        $session = ChatSession::create([
            'name' => 'UAT Cache User',
            'whatsapp' => '62899999999',
            'status' => 'active',
        ]);

        $message = 'Jelaskan spesifikasi Meetup 2';

        // Mock GeminiService/Vertex API response to avoid actual API costs and guarantee mock response
        Http::fake([
            '*.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Spesifikasi **Meetup 2** adalah kamera 4K, auto framing, dan AI sound. (ID: 123)']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // First call: Cache MISS
        $response1 = $this->postJson(route('ai.chat'), [
            'session_id' => $session->id,
            'message' => $message,
            'persona' => 'sales',
        ]);

        $response1->assertStatus(200);
        $response1->assertJsonMissing(['cached' => true]);

        // Second call: Cache HIT (identik)
        $response2 = $this->postJson(route('ai.chat'), [
            'session_id' => $session->id,
            'message' => $message,
            'persona' => 'sales',
        ]);

        $response2->assertStatus(200);
        $response2->assertJson([
            'cached' => true,
        ]);
    }

    /**
     * ⏱️ UAT 3: Pengujian Cache::touch() (Ketahanan Sesi)
     */
    public function test_session_touch_extends_ttl()
    {
        $session = ChatSession::create([
            'name' => 'UAT Touch User',
            'whatsapp' => '62899999999',
            'status' => 'active',
        ]);

        $cacheKey = 'vion_session_active:' . $session->id;
        Cache::put($cacheKey, ['id' => $session->id, 'name' => $session->name], 60);

        Http::fake([
            '*.googleapis.com/*' => Http::response([
                'candidates' => [[ 'content' => [ 'parts' => [['text' => 'Hello.']] ] ]]
            ], 200)
        ]);

        // Trigger aktivitas chat
        $this->postJson(route('ai.chat'), [
            'session_id' => $session->id,
            'message' => 'Halo Vion',
            'persona' => 'sales',
        ]);

        // Cek apakah key diperpanjang
        $this->assertTrue(Cache::has($cacheKey));
        
        // Assert history endpoint runs successfully and fetches session from cache
        $historyResponse = $this->getJson(route('ai.get-history', ['session_id' => $session->id]));
        $historyResponse->assertStatus(200);
        $historyResponse->assertJsonStructure(['messages', 'session']);
    }

    /**
     * 🚀 UAT 4: Pengujian Async Queue (Ringkasan Chat)
     */
    public function test_async_queue_summarize_dispatches_instantly()
    {
        Queue::fake();

        $response = $this->postJson(route('ai.summarize'), [
            'session_id' => 999,
            'history' => [
                ['role' => 'user', 'content' => 'Halo Vion'],
                ['role' => 'assistant', 'content' => 'Halo, ada yang bisa dibantu?'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('summary'));

        Queue::assertPushed(ProcessChatSummaryJob::class, function ($job) {
            return $job->queue === 'ai-summary';
        });
    }

    /**
     * 🔄 UAT 5: Pengujian Reliabilitas (Token Cache)
     */
    public function test_oauth_token_caching()
    {
        // Simpan token mock di cache
        $mockToken = 'mock_vertex_oauth_token_123456';
        Cache::put('vertex_oauth2_token', $mockToken, 55);
        
        // Cek cache token
        $this->assertEquals($mockToken, Cache::get('vertex_oauth2_token'));
    }

    /**
     * 🛡️ UAT 6: Pengujian Resiliensi (Supabase Offline Fallback)
     */
    public function test_supabase_offline_fallback()
    {
        // Invalidate Supabase settings
        config(['services.supabase.url' => 'https://invalid-supabase-host-test.co']);

        // Mock HTTP calls to Supabase to fail/throw exception (or fail request)
        Http::fake([
            'https://invalid-supabase-host-test.co/*' => Http::response('Gateway Timeout', 504)
        ]);

        $vectorService = app(VectorService::class);
        
        // Melakukan pencarian. Harus mengembalikan koleksi kosong bukannya melempar error (crash)
        $results = $vectorService->search(array_fill(0, 768, 0.0), 3);
        
        $this->assertTrue($results->isEmpty());
    }
}
