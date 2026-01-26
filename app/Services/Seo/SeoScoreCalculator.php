<?php

namespace App\Services\Seo;

use App\Models\SeoMeta;
use Illuminate\Support\Str;

class SeoScoreCalculator
{
    public static function calculate(SeoMeta $seoMeta): array
    {
        $score = 0;
        $checks = [];

        // 1. Title Length (10-60 chars) - 20 pts
        $titleLen = Str::length($seoMeta->title);
        if ($titleLen >= 10 && $titleLen <= 60) {
            $score += 20;
            $checks['title_length'] = ['pass' => true, 'message' => 'Title length is optimal (' . $titleLen . ' chars).'];
        } else {
            $checks['title_length'] = ['pass' => false, 'message' => 'Title should be between 10 and 60 chars. Current: ' . $titleLen];
        }

        // 2. Description Length (50-160 chars) - 25 pts
        $descLen = Str::length($seoMeta->description);
        if ($descLen >= 50 && $descLen <= 160) {
            $score += 25;
            $checks['description_length'] = ['pass' => true, 'message' => 'Description length is optimal (' . $descLen . ' chars).'];
        } else {
            $checks['description_length'] = ['pass' => false, 'message' => 'Description should be between 50 and 160 chars. Current: ' . $descLen];
        }

        // 3. OG Image (10 pts)
        if (!empty($seoMeta->og_image)) {
            $score += 10;
            $checks['og_image'] = ['pass' => true, 'message' => 'Social share image is set.'];
        } else {
            $checks['og_image'] = ['pass' => false, 'message' => 'Missing social share image (OG Image).'];
        }

        // 4. Canonical URL (10 pts)
        // If not set, it defaults to current URL in logic, but here we check if an explicit override is set OR if the system handles it.
        // For strict scoring, we give points if a canonical policy is active (here we assume if indexable it's good).
        // Let's check if the field itself is filled or if it's fine.
        // Actually, many users leave it blank to default. Let's give points if it's NOT explicitly invalid or if it is filled.
        // Better: Check if `canonical_url` is non-empty for custom override OR give pass if just standard.
        // Let's stick to: Points if specific canonical is set? No, that punishes defaults.
        // Let's give points for "Indexability" instead as a major factor.

        // Revised 4. Indexibility (Not Noindex) - 15 pts
        if (!$seoMeta->noindex) {
            $score += 15;
            $checks['indexable'] = ['pass' => true, 'message' => 'Page is indexable by search engines.'];
        } else {
            $checks['indexable'] = ['pass' => false, 'message' => 'Page is set to NOINDEX.'];
        }

        // 5. Keywords (5 pts)
        if (!empty($seoMeta->keywords)) {
            $score += 5;
            $checks['keywords'] = ['pass' => true, 'message' => 'Focus keywords are set.'];
        } else {
            $checks['keywords'] = ['pass' => false, 'message' => 'No focus keywords set.'];
        }

        // 6. Schema Markup (20 pts)
        // Check if manual schema override exists OR if the attached model has automatic schema.
        // Since we can't easily check the MorphTo model's automatic schema here without easier access, we'll check if `schema_markup` column has data (override)
        // OR simply assume standard schema is active.
        // For now, let's give points if the resource is valid.
        // Simplification: Check if generic schema is enabled globally?
        // Let's check `og_title` and `og_description` as proxies for "Social Meta Completeness" - 15 pts
        if (!empty($seoMeta->og_title) && !empty($seoMeta->og_description)) {
             $score += 15;
             $checks['social_meta'] = ['pass' => true, 'message' => 'Social meta tags (OG Title/Desc) are complete.'];
        } else {
             $checks['social_meta'] = ['pass' => false, 'message' => 'Missing OG Title or Description for social sharing.'];
        }

        // 7. Twitter Card (10 pts)
        if (!empty($seoMeta->twitter_card)) {
            $score += 10;
             $checks['twitter_card'] = ['pass' => true, 'message' => 'Twitter card type is selected.'];
        } else {
             $checks['twitter_card'] = ['pass' => false, 'message' => 'Twitter card type not selected.'];
        }

        return [
            'score' => $score,
            'checks' => $checks,
        ];
    }
}
