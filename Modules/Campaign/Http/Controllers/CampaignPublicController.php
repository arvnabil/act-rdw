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

        // Inject GTM noscript into body
        $settings = \Modules\Settings\Models\Setting::whereIn('key', ['seo_gtm_id'])->pluck('value', 'key');
        if (!empty($settings['seo_gtm_id'])) {
            $noscript = '
<!-- Google Tag Manager (noscript) --><noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . e($settings['seo_gtm_id']) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript><!-- End Google Tag Manager (noscript) -->
';
            if (stripos($html, '<body') !== false) {
                $html = preg_replace('/(<body[^>]*>)/i', '\g<1>' . $noscript, $html, 1);
            }
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

        $settings = \Modules\Settings\Models\Setting::whereIn('key', ['seo_gtm_id', 'seo_ga4_id'])->pluck('value', 'key');
        if (!empty($settings['seo_gtm_id'])) {
            $gtmId = $settings['seo_gtm_id'];
            $tags .= "
<!-- Google Tag Manager -->
";
            $tags .= "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
";
            $tags .= "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
";
            $tags .= "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
";
            $tags .= "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
";
            $tags .= "})(window,document,'script','dataLayer','" . e($gtmId) . "');</script>
";
            $tags .= "<!-- End Google Tag Manager -->
";
        }
        if (!empty($settings['seo_ga4_id'])) {
            $ga4Id = $settings['seo_ga4_id'];
            $tags .= "
<!-- Google Analytics 4 -->
";
            $tags .= "<script async src="https://www.googletagmanager.com/gtag/js?id=" . e($ga4Id) . ""></script>
";
            $tags .= "<script>
window.dataLayer = window.dataLayer || [];
";
            $tags .= "function gtag(){dataLayer.push(arguments);}
";
            $tags .= "gtag('js', new Date());
";
            $tags .= "gtag('config', '" . e($ga4Id) . "');
</script>
";
        }

        return $tags;
    }
}