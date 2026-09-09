<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Campaign\Models\Campaign;
use Tests\TestCase;

class CampaignRouteTest extends TestCase
{
    use RefreshDatabase;

    // ─── Campaign route isolation ────────────────────────────────────────────

    public function test_draft_campaign_returns_404_on_public_url(): void
    {
        Campaign::create([
            'name'   => 'Draft Campaign',
            'slug'   => 'draft-test',
            'status' => 'draft',
        ]);

        $this->get('/campaign/draft-test')->assertStatus(404);
    }

    public function test_unpublished_campaign_returns_404_on_public_url(): void
    {
        Campaign::create([
            'name'   => 'Unpublished',
            'slug'   => 'unpublished-test',
            'status' => 'unpublished',
        ]);

        $this->get('/campaign/unpublished-test')->assertStatus(404);
    }

    public function test_published_campaign_returns_200(): void
    {
        Campaign::create([
            'name'         => 'Published Campaign',
            'slug'         => 'published-test',
            'status'       => 'published',
            'content_html' => '<h1>Hello World</h1>',
        ]);

        $this->get('/campaign/published-test')->assertStatus(200);
    }

    public function test_published_campaign_renders_html_content(): void
    {
        // Campaign HTML is admin-authored (trusted) — served as-is with SEO injection.
        // JavaScript is preserved so animations and interactions work on the public page.
        Campaign::create([
            'name'         => 'Content Test',
            'slug'         => 'content-test',
            'status'       => 'published',
            'content_html' => '<!DOCTYPE html><html><head><title>Test</title></head><body><h1>Safe Title</h1></body></html>',
        ]);

        $response = $this->get('/campaign/content-test');
        $response->assertStatus(200);
        $response->assertSee('Safe Title');
    }

    public function test_nonexistent_campaign_returns_404(): void
    {
        $this->get('/campaign/does-not-exist')->assertStatus(404);
    }

    public function test_campaign_outside_schedule_returns_404(): void
    {
        Campaign::create([
            'name'      => 'Scheduled',
            'slug'      => 'future-campaign',
            'status'    => 'published',
            'starts_at' => now()->addDays(7),
        ]);

        $this->get('/campaign/future-campaign')->assertStatus(404);
    }

    public function test_expired_campaign_returns_404(): void
    {
        Campaign::create([
            'name'    => 'Expired',
            'slug'    => 'expired-campaign',
            'status'  => 'published',
            'ends_at' => now()->subDays(1),
        ]);

        $this->get('/campaign/expired-campaign')->assertStatus(404);
    }

    // ─── Route regression: existing routes must still work ──────────────────

    public function test_home_route_still_works(): void
    {
        // Home route resolves via CMS PageController — just check it's not 500
        $response = $this->get('/');
        $this->assertNotEquals(500, $response->status());
    }

    public function test_news_route_still_accessible(): void
    {
        $response = $this->get('/news');
        $this->assertNotEquals(500, $response->status());
    }

    public function test_products_route_still_accessible(): void
    {
        $response = $this->get('/products');
        $this->assertNotEquals(500, $response->status());
    }

    public function test_services_route_still_accessible(): void
    {
        $response = $this->get('/services');
        $this->assertNotEquals(500, $response->status());
    }

    public function test_search_route_still_accessible(): void
    {
        $response = $this->get('/search');
        $this->assertNotEquals(500, $response->status());
    }

    public function test_api_routes_still_accessible(): void
    {
        // API login endpoint should respond (even if 422 for missing credentials)
        $response = $this->postJson('/api/auth/login', []);
        $this->assertNotEquals(500, $response->status());
    }

    public function test_campaign_route_does_not_conflict_with_cms_dynamic_resolver(): void
    {
        // /campaign/{slug} should be handled by CampaignPublicController, not CMS
        // If no campaign exists → 404 from Campaign controller (not CMS)
        $response = $this->get('/campaign/some-random-slug');
        $this->assertSame(404, $response->status());
    }
}