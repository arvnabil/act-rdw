<?php

namespace Tests\Unit;

use Modules\Campaign\Services\CampaignSanitizer;
use PHPUnit\Framework\TestCase;

class CampaignSanitizerTest extends TestCase
{
    private CampaignSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new CampaignSanitizer();
    }

    // ─── XSS / Security tests ────────────────────────────────────────────────

    public function test_removes_script_tags(): void
    {
        $input  = '<script>alert(1)</script>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('alert(1)', $output);
    }

    public function test_removes_script_with_content(): void
    {
        $input  = '<p>Hello</p><script>document.cookie="stolen";</script><p>World</p>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('document.cookie', $output);
        $this->assertStringContainsString('Hello', $output);
        $this->assertStringContainsString('World', $output);
    }

    public function test_removes_onclick_inline_handler(): void
    {
        $input  = '<div onclick="alert(1)">Click me</div>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('onclick', $output);
    }

    public function test_removes_onload_inline_handler(): void
    {
        $input  = '<body onload="stealData()"><p>Content</p></body>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('onload', $output);
    }

    public function test_removes_onerror_inline_handler(): void
    {
        $input  = '<img src="x" onerror="alert(1)">';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('onerror', $output);
    }

    public function test_removes_javascript_href(): void
    {
        $input  = '<a href="javascript:alert(1)">Click</a>';
        $output = $this->sanitizer->sanitize($input);
        // The href with javascript: should be removed/sanitized
        $this->assertStringNotContainsString('javascript:', $output);
    }

    public function test_removes_iframe(): void
    {
        $input  = '<iframe src="https://evil.com/payload"></iframe>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('<iframe', $output);
    }

    public function test_removes_object_embed(): void
    {
        $input  = '<object data="payload.swf"></object><embed src="evil.swf">';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('<object', $output);
        $this->assertStringNotContainsString('<embed', $output);
    }

    public function test_style_tag_with_dangerous_js_does_not_allow_script(): void
    {
        // <style> is allowed, but <script> is never allowed
        $input  = '<style>body{color:red}</style><script>alert(1)</script>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('alert(1)', $output);
    }

    public function test_preserves_style_tag(): void
    {
        // <style> is now allowed for full-page HTML campaigns
        $input  = '<style>.hero { color: #333; font-size: 2rem; }</style><h1>Title</h1>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('<style', $output);
        $this->assertStringContainsString('.hero', $output);
        $this->assertStringContainsString('Title', $output);
    }

    // ─── Safe content preservation tests ────────────────────────────────────

    public function test_preserves_safe_headings(): void
    {
        $input  = '<h1>Campaign Title</h1><h2>Sub Title</h2>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('Campaign Title', $output);
        $this->assertStringContainsString('Sub Title', $output);
    }

    public function test_preserves_paragraph(): void
    {
        $input  = '<p>This is safe campaign content.</p>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('safe campaign content', $output);
    }

    public function test_preserves_safe_link(): void
    {
        $input  = '<a href="/contact" class="btn-primary">Hubungi Kami</a>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('href="/contact"', $output);
        $this->assertStringContainsString('Hubungi Kami', $output);
    }

    public function test_preserves_https_link(): void
    {
        $input  = '<a href="https://example.com">External Link</a>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('https://example.com', $output);
    }

    public function test_preserves_image(): void
    {
        $input  = '<img src="/assets/hero.jpg" alt="Hero">';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('/assets/hero.jpg', $output);
        $this->assertStringContainsString('alt="Hero"', $output);
    }

    public function test_preserves_unordered_list(): void
    {
        $input  = '<ul><li>Feature A</li><li>Feature B</li></ul>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('Feature A', $output);
        $this->assertStringContainsString('Feature B', $output);
    }

    public function test_preserves_section_div_with_class(): void
    {
        $input  = '<section class="hero"><div class="container"><p>Content</p></div></section>';
        $output = $this->sanitizer->sanitize($input);
        $this->assertStringContainsString('class="hero"', $output);
        $this->assertStringContainsString('Content', $output);
    }

    public function test_empty_string_returns_empty(): void
    {
        $output = $this->sanitizer->sanitize('');
        $this->assertSame('', $output);
    }
}