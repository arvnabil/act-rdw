<?php

namespace Modules\Campaign\Http\Controllers;

use Modules\Campaign\Models\Campaign;
use Modules\Campaign\Services\CampaignSeoService;
use Illuminate\Routing\Controller;

/**
 * Serves the public campaign landing page.
 *
 * Campaign HTML is admin-authored content (trusted), not user-generated.
 * We serve the raw HTML directly (preserving CSS, JS, fonts, animations)
 * and inject our SEO meta tags + JSON-LD into the <head>.
 *
 * Security note: since only authenticated admins can create/edit campaigns,
 * the HTML content is treated as trusted. This is the same model used by
 * WordPress pages, Webflow exports, and similar CMS landing-page builders.
 */
class CampaignPublicController extends Controller
{
    public function __construct(
        private CampaignSeoService $seoService,
    ) {}

    public function show(string $slug)
    {
        $campaign = Campaign::where('slug', $slug)->first();

        if (! $campaign || ! $campaign->isPubliclyVisible()) {
            abort(404);
        }

        $html   = $campaign->content_html ?? '';
        $meta   = $this->seoService->getMetaTags($campaign);
        $jsonLd = $this->seoService->getJsonLd($campaign);

        // ─── Inject SEO meta tags into <head> ────────────────────────────────
        $seoTags = $this->buildSeoTags($meta, $jsonLd);

        if (stripos($html, '</head>') !== false) {
            // Inject before closing </head>
            $html = str_ireplace('</head>', $seoTags . '</head>', $html);
        } elseif (stripos($html, '<head>') !== false) {
            // Append after opening <head>
            $html = str_ireplace('<head>', '<head>' . $seoTags, $html);
        } else {
            // No head element — prepend to document
            $html = $seoTags . $html;
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function buildSeoTags(array $meta, array $jsonLd): string
    {
        $tags = '';

        // Override/add title if not already in document
        $tags .= "\n<!-- Campaign SEO — injected by ACT RDW -->\n";
        $tags .= '<meta name="description" content="' . e($meta['description']) . '">' . "\n";
        $tags .= '<meta name="robots" content="' . e($meta['robots']) . '">' . "\n";
        $tags .= '<link rel="canonical" href="' . e($meta['canonical']) . '">' . "\n";

        // Open Graph
        $tags .= '<meta property="og:type" content="' . e($meta['og_type']) . '">' . "\n";
        $tags .= '<meta property="og:title" content="' . e($meta['og_title']) . '">' . "\n";
        $tags .= '<meta property="og:description" content="' . e($meta['og_description']) . '">' . "\n";
        $tags .= '<meta property="og:url" content="' . e($meta['og_url']) . '">' . "\n";
        if (! empty($meta['og_image'])) {
            $tags .= '<meta property="og:image" content="' . e(asset($meta['og_image'])) . '">' . "\n";
        }

        // JSON-LD Structured Data
        foreach ($jsonLd as $schema) {
            $tags .= '<script type="application/ld+json">'
                . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                . '</script>' . "\n";
        }

        return $tags;
    }
}