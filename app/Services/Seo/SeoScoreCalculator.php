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

        // Fetch parent model context if available
        $parent = $seoMeta->seoable;
        
        $title = $seoMeta->title ?: ($parent->title ?? $parent->name ?? '');
        $description = $seoMeta->description ?: ($parent->excerpt ?? $parent->description ?? '');
        $ogImage = $seoMeta->og_image ?: ($parent->featured_image ?? $parent->thumbnail ?? $parent->image_path ?? '');

        // 1. Title Length (10-60 chars) - 20 pts
        $titleLen = Str::length($title);
        if ($titleLen >= 10 && $titleLen <= 60) {
            $score += 20;
            $checks['title_length'] = ['pass' => true, 'message' => 'Title length is optimal (' . $titleLen . ' chars).'];
        } else {
            $checks['title_length'] = ['pass' => false, 'message' => 'Title should be between 10 and 60 chars. Current: ' . $titleLen];
        }

        // 2. Description Length (50-160 chars) - 25 pts
        $descLen = Str::length($description);
        if ($descLen >= 50 && $descLen <= 160) {
            $score += 25;
            $checks['description_length'] = ['pass' => true, 'message' => 'Description length is optimal (' . $descLen . ' chars).'];
        } else {
            $checks['description_length'] = ['pass' => false, 'message' => 'Description should be between 50 and 160 chars. Current: ' . $descLen];
        }

        // 3. OG Image (10 pts)
        if (!empty($ogImage)) {
            $score += 10;
            $checks['og_image'] = ['pass' => true, 'message' => 'Social share image is set (via manual or fallback).'];
        } else {
            $checks['og_image'] = ['pass' => false, 'message' => 'Missing social share image (OG Image).'];
        }

        // 4. Indexibility (Not Noindex) - 15 pts
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

        // 6. Social Meta (15 pts)
        $ogTitle = $seoMeta->og_title ?: $title;
        $ogDesc = $seoMeta->og_description ?: $description;
        if (!empty($ogTitle) && !empty($ogDesc)) {
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
