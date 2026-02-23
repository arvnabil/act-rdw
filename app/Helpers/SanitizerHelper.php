<?php

namespace App\Helpers;

class SanitizerHelper
{
    /**
     * Sanitize HTML content to prevent XSS.
     * Allows a safe subset of tags for CMS content.
     */
    public static function sanitizeHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Whitelist of allowed tags for the page builder/CMS content
        $allowedTags = '<div><span><p><br><b><strong><i><em><ul><ol><li><a><img><h1><h2><h3><h4><h5><h6><blockquote><code><pre><hr><table><thead><tbody><tr><th><td>';

        // Strip disallowed tags
        $sanitized = strip_tags($html, $allowedTags);

        // Additional attribute cleaning could be done here if needed
        // For a more robust solution, HTMLPurifier is recommended.
        
        return $sanitized;
    }
}
