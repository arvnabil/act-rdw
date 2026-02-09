<?php

namespace App\Services\Seo;

use App\Models\Page;
use App\Services\JsonLdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Unified SEO Audit Service
 * 
 * This service provides a comprehensive SEO audit based on Google's
 * best practices and Core Web Vitals recommendations.
 * 
 * Scoring is deductive: starts at 100 and deducts points for issues.
 */
class SeoAuditService
{
    protected JsonLdGenerator $jsonLdGenerator;

    public function __construct(JsonLdGenerator $jsonLdGenerator)
    {
        $this->jsonLdGenerator = $jsonLdGenerator;
    }

    /**
     * Perform a full SEO audit on the given model.
     * 
     * @param Model $model The model to audit (Page, News, Product, etc.)
     * @return array ['score' => int, 'issues' => array, 'checks' => array]
     */
    public function audit(Model $model): array
    {
        $issues = [];
        $checks = [];
        $score = 100;

        // Get SEO meta relationship
        $seo = $model->seo;

        // Resolve values with fallbacks
        $title = $seo?->title ?? $model->title ?? $model->name ?? '';
        $description = $seo?->description ?? $model->excerpt ?? $model->description ?? '';
        $ogImage = $seo?->og_image ?? $model->featured_image ?? $model->thumbnail ?? $model->image_path ?? '';
        $ogTitle = $seo?->og_title ?? $title;
        $ogDescription = $seo?->og_description ?? $description;

        // --- Meta Tag Audits ---
        $this->auditTitle($title, $seo, $issues, $checks, $score);
        $this->auditDescription($description, $seo, $issues, $checks, $score);
        $this->auditNoIndex($seo, $issues, $checks, $score);

        // --- Social Meta Audits ---
        $this->auditOgImage($ogImage, $issues, $checks, $score);
        $this->auditSocialMeta($ogTitle, $ogDescription, $issues, $checks, $score);
        $this->auditTwitterCard($seo, $issues, $checks, $score);

        // --- Advanced SEO Audits ---
        $this->auditKeywords($seo, $issues, $checks, $score);
        $this->auditCanonical($seo, $model, $issues, $checks, $score);

        // --- JSON-LD / Structured Data Audits ---
        $this->auditJsonLd($model, $seo, $issues, $checks, $score);

        return [
            'score' => max(0, $score),
            'issues' => $issues,
            'checks' => $checks,
        ];
    }

    /**
     * Calculate score only (for saving to database).
     * This is a lightweight version that doesn't include issue details.
     */
    public function calculateScore(Model $model): int
    {
        $result = $this->audit($model);
        return $result['score'];
    }

    // ========================================================================
    // AUDIT METHODS
    // ========================================================================

    protected function auditTitle(string $title, $seo, array &$issues, array &$checks, int &$score): void
    {
        $titleLen = Str::length($title);

        if (empty($title)) {
            $issues[] = [
                'severity' => 'error',
                'message' => 'Missing Meta Title',
                'recommendation' => 'Add a Meta Title. Google displays up to 60 characters.',
            ];
            $checks['title'] = ['pass' => false, 'message' => 'Meta title is missing.'];
            $score -= 20;
        } elseif ($titleLen < 30) {
            $issues[] = [
                'severity' => 'warning',
                'message' => 'Meta Title too short (' . $titleLen . ' chars)',
                'recommendation' => 'Increase title length to at least 30-60 characters for better visibility.',
            ];
            $checks['title'] = ['pass' => false, 'message' => 'Title is too short (' . $titleLen . ' chars).'];
            $score -= 10;
        } elseif ($titleLen > 60) {
            $issues[] = [
                'severity' => 'warning',
                'message' => 'Meta Title too long (' . $titleLen . ' chars)',
                'recommendation' => 'Keep title under 60 characters to avoid truncation in search results.',
            ];
            $checks['title'] = ['pass' => false, 'message' => 'Title is too long (' . $titleLen . ' chars).'];
            $score -= 5;
        } else {
            $checks['title'] = ['pass' => true, 'message' => 'Title length is optimal (' . $titleLen . ' chars).'];
        }
    }

    protected function auditDescription(string $description, $seo, array &$issues, array &$checks, int &$score): void
    {
        $descLen = Str::length($description);

        if (empty($description)) {
            $issues[] = [
                'severity' => 'warning',
                'message' => 'Missing Meta Description',
                'recommendation' => 'Add a custom Meta Description (70-160 chars) for better CTR in search results.',
            ];
            $checks['description'] = ['pass' => false, 'message' => 'Meta description is missing.'];
            $score -= 10;
        } elseif ($descLen < 70) {
            $issues[] = [
                'severity' => 'info',
                'message' => 'Meta Description short (' . $descLen . ' chars)',
                'recommendation' => 'Expand description to 70-160 characters for optimal visibility.',
            ];
            $checks['description'] = ['pass' => false, 'message' => 'Description is too short (' . $descLen . ' chars).'];
            $score -= 5;
        } elseif ($descLen > 160) {
            $issues[] = [
                'severity' => 'warning',
                'message' => 'Meta Description too long (' . $descLen . ' chars)',
                'recommendation' => 'Truncate description to 160 characters to prevent Google from cutting it off.',
            ];
            $checks['description'] = ['pass' => false, 'message' => 'Description is too long (' . $descLen . ' chars).'];
            $score -= 5;
        } else {
            $checks['description'] = ['pass' => true, 'message' => 'Description length is optimal (' . $descLen . ' chars).'];
        }
    }

    protected function auditNoIndex($seo, array &$issues, array &$checks, int &$score): void
    {
        if ($seo?->noindex) {
            $issues[] = [
                'severity' => 'error',
                'message' => 'Page is No-Indexed',
                'recommendation' => 'Remove NoIndex if this is a public page that should appear in search results.',
            ];
            $checks['indexable'] = ['pass' => false, 'message' => 'Page is set to NOINDEX.'];
            $score -= 15;
        } else {
            $checks['indexable'] = ['pass' => true, 'message' => 'Page is indexable by search engines.'];
        }
    }

    protected function auditOgImage(string $ogImage, array &$issues, array &$checks, int &$score): void
    {
        if (empty($ogImage)) {
            $issues[] = [
                'severity' => 'warning',
                'message' => 'Missing Social Share Image (OG Image)',
                'recommendation' => 'Add an OG Image (1200x630px recommended) for better social media engagement.',
            ];
            $checks['og_image'] = ['pass' => false, 'message' => 'Missing OG Image for social sharing.'];
            $score -= 10;
        } else {
            $checks['og_image'] = ['pass' => true, 'message' => 'Social share image is set.'];
        }
    }

    protected function auditSocialMeta(string $ogTitle, string $ogDescription, array &$issues, array &$checks, int &$score): void
    {
        if (empty($ogTitle) || empty($ogDescription)) {
            $issues[] = [
                'severity' => 'info',
                'message' => 'Incomplete Social Meta Tags',
                'recommendation' => 'Set both OG Title and OG Description for complete social media previews.',
            ];
            $checks['social_meta'] = ['pass' => false, 'message' => 'Missing OG Title or Description.'];
            $score -= 5;
        } else {
            $checks['social_meta'] = ['pass' => true, 'message' => 'Social meta tags are complete.'];
        }
    }

    protected function auditTwitterCard($seo, array &$issues, array &$checks, int &$score): void
    {
        if (empty($seo?->twitter_card)) {
            $issues[] = [
                'severity' => 'info',
                'message' => 'Twitter Card type not set',
                'recommendation' => 'Set Twitter Card type (summary_large_image recommended) for better Twitter/X previews.',
            ];
            $checks['twitter_card'] = ['pass' => false, 'message' => 'Twitter card type not selected.'];
            $score -= 5;
        } else {
            $checks['twitter_card'] = ['pass' => true, 'message' => 'Twitter card type is set: ' . $seo->twitter_card];
        }
    }

    protected function auditKeywords($seo, array &$issues, array &$checks, int &$score): void
    {
        $keywords = $seo?->keywords;

        if (empty($keywords) || (is_array($keywords) && count($keywords) === 0)) {
            $issues[] = [
                'severity' => 'info',
                'message' => 'No Focus Keywords set',
                'recommendation' => 'Define focus keywords to help track content optimization.',
            ];
            $checks['keywords'] = ['pass' => false, 'message' => 'No focus keywords set.'];
            $score -= 5;
        } else {
            $checks['keywords'] = ['pass' => true, 'message' => 'Focus keywords are defined.'];
        }
    }

    protected function auditCanonical($seo, Model $model, array &$issues, array &$checks, int &$score): void
    {
        // Canonical is optional but good to have for duplicate content prevention
        // We won't deduct points, just inform
        if (!empty($seo?->canonical_url)) {
            $checks['canonical'] = ['pass' => true, 'message' => 'Canonical URL is explicitly set.'];
        } else {
            $checks['canonical'] = ['pass' => true, 'message' => 'Canonical URL will default to page URL.'];
        }
    }

    protected function auditJsonLd(Model $model, $seo, array &$issues, array &$checks, int &$score): void
    {
        // Skip JSON-LD audit if page is noindex (it won't be crawled anyway)
        if ($seo?->noindex) {
            $checks['json_ld'] = ['pass' => true, 'message' => 'JSON-LD skipped (page is noindex).'];
            return;
        }

        try {
            $result = $this->jsonLdGenerator->generate($model, true);
            $schemas = $result['@graph'] ?? [];

            $hasOrganization = false;
            $hasWebPage = false;
            $hasMainEntity = false;
            $organizationId = url('/') . '#organization';

            foreach ($schemas as $schema) {
                $type = $schema['@type'] ?? '';

                if ($type === 'Organization') {
                    $hasOrganization = true;
                    if (($schema['@id'] ?? '') !== $organizationId) {
                        $issues[] = [
                            'severity' => 'warning',
                            'message' => 'Inconsistent Organization @id',
                            'recommendation' => 'Organization @id should match the global ID for proper linking.',
                        ];
                        $score -= 5;
                    }
                }

                if ($type === 'WebPage') {
                    $hasWebPage = true;
                    if (!isset($schema['isPartOf'])) {
                        $issues[] = [
                            'severity' => 'warning',
                            'message' => 'WebPage missing isPartOf',
                            'recommendation' => 'Link WebPage to WebSite (isPartOf) for better schema connectivity.',
                        ];
                        $score -= 5;
                    }
                }

                // Check for main entity types
                if (in_array($type, ['Article', 'NewsArticle', 'Product', 'Service', 'Event'])) {
                    $hasMainEntity = true;
                }
            }

            // Homepage should have Organization schema
            if ($model instanceof Page && $model->is_homepage && !$hasOrganization) {
                $issues[] = [
                    'severity' => 'warning',
                    'message' => 'Homepage missing Organization Schema',
                    'recommendation' => 'Add Organization schema to homepage for brand recognition in search.',
                ];
                $score -= 10;
            }

            // Pages should generally have WebPage or a specific type
            if (!$hasWebPage && !$hasMainEntity) {
                $issues[] = [
                    'severity' => 'info',
                    'message' => 'No specific structured data detected',
                    'recommendation' => 'Consider adding WebPage, Article, or Product schema for richer search results.',
                ];
                $score -= 5;
            } else {
                $checks['json_ld'] = ['pass' => true, 'message' => 'Structured data (JSON-LD) is present.'];
            }

        } catch (\Exception $e) {
            $issues[] = [
                'severity' => 'error',
                'message' => 'JSON-LD generation failed',
                'recommendation' => 'Check the JsonLdGenerator service for errors: ' . Str::limit($e->getMessage(), 50),
            ];
            $score -= 10;
        }
    }
}
