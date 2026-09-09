<?php

namespace Tests\Unit;

use Modules\Campaign\Services\CampaignHtmlParser;
use PHPUnit\Framework\TestCase;

class CampaignHtmlParserTest extends TestCase
{
    private CampaignHtmlParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new CampaignHtmlParser();
    }

    // ─── parse() tests ───────────────────────────────────────────────────────

    public function test_parse_returns_empty_sections_for_empty_html(): void
    {
        $result = $this->parser->parse('');
        $this->assertSame(['sections' => []], $result);
    }

    public function test_parse_detects_heading(): void
    {
        $result = $this->parser->parse('<h1>Campaign Title</h1>');
        $this->assertNotEmpty($result['sections']);
        $heading = $result['sections'][0];
        $this->assertSame('heading', $heading['type']);
        $this->assertSame(1, $heading['level']);
        $this->assertSame('Campaign Title', $heading['text']);
    }

    public function test_parse_detects_h2_heading(): void
    {
        $result = $this->parser->parse('<h2>Sub Heading</h2>');
        $heading = $result['sections'][0];
        $this->assertSame('heading', $heading['type']);
        $this->assertSame(2, $heading['level']);
    }

    public function test_parse_detects_paragraph(): void
    {
        $result = $this->parser->parse('<p>Some campaign text</p>');
        $section = $result['sections'][0];
        $this->assertSame('text', $section['type']);
        $this->assertSame('Some campaign text', $section['text']);
    }

    public function test_parse_detects_image(): void
    {
        $result = $this->parser->parse('<img src="/img/hero.jpg" alt="Hero Image">');
        $section = $result['sections'][0];
        $this->assertSame('image', $section['type']);
        $this->assertSame('/img/hero.jpg', $section['src']);
        $this->assertSame('Hero Image', $section['alt']);
    }

    public function test_parse_detects_button(): void
    {
        $result = $this->parser->parse('<button>Konsultasi Sekarang</button>');
        $section = $result['sections'][0];
        $this->assertSame('button', $section['type']);
        $this->assertSame('Konsultasi Sekarang', $section['label']);
    }

    public function test_parse_detects_section_block(): void
    {
        $result = $this->parser->parse('<section class="hero"><h1>Hello</h1></section>');
        $section = $result['sections'][0];
        $this->assertSame('section', $section['type']);
        $this->assertNotEmpty($section['children']);
        $this->assertSame('hero', $section['class']);
    }

    public function test_parse_detects_unordered_list(): void
    {
        $html   = '<ul><li>Feature A</li><li>Feature B</li></ul>';
        $result = $this->parser->parse($html);
        $section = $result['sections'][0];
        $this->assertSame('unordered_list', $section['type']);
        $this->assertContains('Feature A', $section['items']);
        $this->assertContains('Feature B', $section['items']);
    }

    public function test_parse_handles_malformed_html_gracefully(): void
    {
        // Should not throw, should return best-effort parse
        $result = $this->parser->parse('<div><p>Unclosed');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('sections', $result);
    }

    // ─── detectCtas() tests ──────────────────────────────────────────────────

    public function test_detect_ctas_returns_empty_for_empty_html(): void
    {
        $result = $this->parser->detectCtas('');
        $this->assertSame([], $result);
    }

    public function test_detect_ctas_finds_button(): void
    {
        $html   = '<button>Konsultasi Sekarang</button>';
        $ctas   = $this->parser->detectCtas($html);
        $this->assertCount(1, $ctas);
        $this->assertSame('button', $ctas[0]['type']);
        $this->assertSame('Konsultasi Sekarang', $ctas[0]['label']);
    }

    public function test_detect_ctas_finds_link(): void
    {
        $html = '<a href="/contact">Hubungi Kami</a>';
        $ctas = $this->parser->detectCtas($html);
        $this->assertCount(1, $ctas);
        $this->assertSame('link', $ctas[0]['type']);
        $this->assertSame('Hubungi Kami', $ctas[0]['label']);
        $this->assertSame('/contact', $ctas[0]['url']);
    }

    public function test_detect_ctas_classifies_btn_class_as_cta_link(): void
    {
        $html = '<a href="/contact" class="btn-primary">Hubungi Kami</a>';
        $ctas = $this->parser->detectCtas($html);
        $this->assertCount(1, $ctas);
        $this->assertSame('cta_link', $ctas[0]['type']);
    }

    public function test_detect_ctas_skips_anchor_links(): void
    {
        $html = '<a href="#section">Jump to Section</a>';
        $ctas = $this->parser->detectCtas($html);
        $this->assertCount(0, $ctas);
    }

    public function test_detect_ctas_skips_javascript_href(): void
    {
        $html = '<a href="javascript:alert(1)">Click</a>';
        $ctas = $this->parser->detectCtas($html);
        $this->assertCount(0, $ctas);
    }

    public function test_detect_ctas_skips_empty_label_links(): void
    {
        $html = '<a href="/contact"></a>';
        $ctas = $this->parser->detectCtas($html);
        $this->assertCount(0, $ctas);
    }
}