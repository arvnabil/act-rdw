<?php

namespace Modules\Campaign\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use DOMNodeList;

/**
 * Sanitizes raw HTML campaign content.
 *
 * Strategy:
 *  - Uses DOMDocument for a whitelist pass that KEEPS <style> blocks,
 *    <link rel="stylesheet">, and all visual elements needed for full-page HTML campaigns.
 *  - Drops: <script>, <iframe>, <object>, <embed>, inline event handlers (on*),
 *    and javascript: hrefs.
 *  - Does NOT use Symfony HtmlSanitizer (it strips <style> unconditionally).
 */
class CampaignSanitizer
{
    /**
     * HTML elements to REMOVE completely (including all children).
     */
    private const DROP_ELEMENTS = [
        'script', 'iframe', 'object', 'embed', 'applet', 'base', 'bgsound',
        'frame', 'frameset', 'noframes', 'noscript',
    ];

    /**
     * Attribute name patterns to strip from ALL elements.
     * Catches onclick, onload, onerror, onmouseover, onfocus, etc.
     */
    private const DROP_ATTRIBUTE_PATTERNS = [
        '/^on/i',           // all on* event handlers
        '/^javascript:/i',  // should not appear as attr name, but guard
    ];

    /**
     * Href/src attribute values that are dangerous.
     */
    private const DANGEROUS_URL_SCHEMES = [
        'javascript:',
        'vbscript:',
        'data:text/html',
    ];

    /**
     * Sanitize raw HTML — removes scripts, event handlers, and javascript: URLs.
     * Preserves <style>, <link rel="stylesheet">, fonts, CSS variables, animations.
     */
    public function sanitize(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Detect if this is a full HTML document
        $isFullDoc = preg_match('/<html[\s>]/i', $html) || preg_match('/<!doctype/i', $html);

        if ($isFullDoc) {
            // Load as-is (already has DOCTYPE/html wrapper)
            $dom->loadHTML($html, LIBXML_NOERROR);
        } else {
            // Wrap in a minimal document for parsing
            $dom->loadHTML(
                '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>',
                LIBXML_NOERROR
            );
        }

        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // ─── 1. Remove all DROP_ELEMENTS ─────────────────────────────────────
        foreach (self::DROP_ELEMENTS as $tag) {
            foreach (iterator_to_array($xpath->query('//' . $tag) ?? new \EmptyIterator()) as $node) {
                if ($node->parentNode) {
                    $node->parentNode->removeChild($node);
                }
            }
        }

        // ─── 2. Strip dangerous attributes from all elements ─────────────────
        /** @var DOMElement[] $allElements */
        $allElements = $xpath->query('//*');
        if ($allElements) {
            foreach (iterator_to_array($allElements) as $element) {
                if (! ($element instanceof DOMElement)) {
                    continue;
                }

                $attrsToRemove = [];
                foreach ($element->attributes as $attr) {
                    $name  = strtolower($attr->name);
                    $value = $attr->value;

                    // Drop on* event handlers
                    if (preg_match('/^on/i', $name)) {
                        $attrsToRemove[] = $attr->name;
                        continue;
                    }

                    // Drop dangerous URL schemes from href/src/action/formaction
                    if (in_array($name, ['href', 'src', 'action', 'formaction', 'data', 'xlink:href'])) {
                        $valueLower = strtolower(trim($value));
                        foreach (self::DANGEROUS_URL_SCHEMES as $scheme) {
                            if (str_starts_with($valueLower, $scheme)) {
                                $attrsToRemove[] = $attr->name;
                                break;
                            }
                        }
                    }
                }

                foreach ($attrsToRemove as $attrName) {
                    $element->removeAttribute($attrName);
                }
            }
        }

        // ─── 3. Serialize back ───────────────────────────────────────────────
        if ($isFullDoc) {
            // Serialize full document
            $result = $dom->saveHTML();
        } else {
            // Extract only the body content
            $body = $dom->getElementsByTagName('body')->item(0);
            if (! $body) {
                return '';
            }
            $result = '';
            foreach ($body->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }
        }

        return $result;
    }
}