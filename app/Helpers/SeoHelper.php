<?php

namespace App\Helpers;

use App\Services\SeoLinkParser;

class SeoHelper
{
    /**
     * Automatically parses HTML content to secure external links and handle SEO.
     * Use this in Blade: {!! \App\Helpers\SeoHelper::parse_links($post->content) !!}
     */
    public static function parse_links(?string $html): string
    {
        if (!$html) {
            return '';
        }

        return SeoLinkParser::parse($html);
    }

    /**
     * Get the correct rel attribute for a single URL based on SEO rules and whitelist.
     */
    public static function get_rel(?string $url): string
    {
        return SeoLinkParser::getRelAttribute($url);
    }
}
