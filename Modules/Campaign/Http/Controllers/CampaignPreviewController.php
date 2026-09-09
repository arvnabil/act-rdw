<?php

namespace Modules\Campaign\Http\Controllers;

use Modules\Campaign\Models\Campaign;
use Modules\Campaign\Services\CampaignSeoService;
use Illuminate\Routing\Controller;

class CampaignPreviewController extends Controller
{
    public function __construct(
        private CampaignSeoService $seoService,
    ) {}

    /**
     * Serve the raw campaign HTML directly as a full document.
     *
     * We inject a preview banner overlay just before </body> so that:
     * - All campaign CSS, fonts, JS animations work perfectly (no sandbox).
     * - Admin can see an exact 1:1 preview of how the campaign will look.
     * - The preview route is auth-protected and noindex.
     */
    public function preview(int $id)
    {
        $campaign = Campaign::findOrFail($id);

        $html = $campaign->content_html ?? '';

        // Build the preview banner snippet to inject
        $status      = strtoupper(e($campaign->status));
        $name        = e($campaign->name);
        $backUrl     = e(url('/activioncms/campaigns/' . $id . '/edit'));

        $bannerScript = <<<BANNER
<style>
#__campaign_preview_banner {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important;
    z-index: 2147483647 !important;
    background: #f59e0b !important;
    color: #1c1917 !important;
    font-family: system-ui, -apple-system, sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    padding: 8px 20px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
}
#__campaign_preview_banner .badge {
    background: #1c1917; color: #f59e0b; border-radius: 4px;
    padding: 2px 8px; font-size: 11px; letter-spacing: 0.05em;
}
#__campaign_preview_banner a {
    color: #1c1917; font-size: 12px; margin-left: auto;
}
body { padding-top: 44px !important; }
</style>
<div id="__campaign_preview_banner">
    <span>⚠ PREVIEW MODE</span>
    <span class="badge">{$status}</span>
    <span>{$name}</span>
    <a href="{$backUrl}">← Back to Admin</a>
</div>
BANNER;

        // Inject before </body> if present, otherwise append
        if (stripos($html, '</body>') !== false) {
            $html = str_ireplace('</body>', $bannerScript . '</body>', $html);
        } else {
            $html .= $bannerScript;
        }

        // Inject noindex meta into <head> if present
        $noindex = '<meta name="robots" content="noindex, nofollow">';
        if (stripos($html, '</head>') !== false) {
            $html = str_ireplace('</head>', $noindex . '</head>', $html);
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}