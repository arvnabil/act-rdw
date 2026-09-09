<?php

namespace Modules\Campaign\Services;

use DOMDocument;
use DOMXPath;
use DOMElement;

/**
 * Parses HTML campaign content into a structured JSON representation.
 * Uses DOMDocument (PHP built-in) — NO regex as primary parsing mechanism.
 */
class CampaignHtmlParser
{
    /** Known CTA CSS class patterns. */
    private const CTA_CLASS_PATTERNS = [
        'btn', 'button', 'cta', 'btn-primary', 'btn-secondary',
        'btn-cta', 'call-to-action', 'btn-outline', 'btn-action',
    ];

    /**
     * Parse raw HTML and return a structured content_json array.
     */
    public function parse(string $html): array
    {
        if (empty(trim($html))) {
            return ['sections' => []];
        }

        $dom  = $this->loadDom($html);
        $body = $dom->getElementsByTagName('body')->item(0);

        if (! $body) {
            return ['sections' => []];
        }

        $sections = [];
        foreach ($body->childNodes as $child) {
            if (! ($child instanceof DOMElement)) {
                continue;
            }
            $sections[] = $this->parseNode($child);
        }

        return ['sections' => array_values(array_filter($sections))];
    }

    /**
     * Detect CTA candidates (buttons and actionable links) from HTML.
     *
     * @return array<int, array{type: string, label: string, url: string, element_id: string|null, class: string}>
     */
    public function detectCtas(string $html): array
    {
        if (empty(trim($html))) {
            return [];
        }

        $dom   = $this->loadDom($html);
        $xpath = new DOMXPath($dom);
        $ctas  = [];

        // Detect <button> elements
        foreach ($xpath->query('//button') as $node) {
            /** @var DOMElement $node */
            $ctas[] = [
                'type'       => 'button',
                'label'      => trim($node->textContent),
                'url'        => '',
                'element_id' => $node->getAttribute('id') ?: null,
                'class'      => $node->getAttribute('class'),
            ];
        }

        // Detect <a> elements with href
        foreach ($xpath->query('//a[@href]') as $node) {
            /** @var DOMElement $node */
            $href = $node->getAttribute('href');

            if (
                empty($href)
                || str_starts_with(trim($href), '#')
                || str_starts_with(trim(strtolower($href)), 'javascript:')
            ) {
                continue;
            }

            $class = $node->getAttribute('class');
            $label = trim($node->textContent);

            if (empty($label)) {
                continue;
            }

            $ctas[] = [
                'type'       => $this->isCtaClass($class) ? 'cta_link' : 'link',
                'label'      => $label,
                'url'        => $href,
                'element_id' => $node->getAttribute('id') ?: null,
                'class'      => $class,
            ];
        }

        return $ctas;
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function parseNode(DOMElement $node): array
    {
        $tag   = strtolower($node->nodeName);
        $id    = $node->getAttribute('id') ?: null;
        $class = $node->getAttribute('class') ?: null;

        if (in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            return [
                'type'  => 'heading',
                'level' => (int) substr($tag, 1),
                'text'  => trim($node->textContent),
                'id'    => $id,
                'class' => $class,
            ];
        }

        if ($tag === 'p') {
            return [
                'type'  => 'text',
                'text'  => trim($node->textContent),
                'html'  => $this->innerHtml($node),
                'id'    => $id,
                'class' => $class,
            ];
        }

        if ($tag === 'img') {
            return [
                'type'  => 'image',
                'src'   => $node->getAttribute('src'),
                'alt'   => $node->getAttribute('alt'),
                'id'    => $id,
                'class' => $class,
            ];
        }

        if ($tag === 'a') {
            return [
                'type'  => 'link',
                'label' => trim($node->textContent),
                'url'   => $node->getAttribute('href'),
                'id'    => $id,
                'class' => $class,
            ];
        }

        if ($tag === 'button') {
            return [
                'type'  => 'button',
                'label' => trim($node->textContent),
                'id'    => $id,
                'class' => $class,
            ];
        }

        if (in_array($tag, ['ul', 'ol'])) {
            return [
                'type'  => $tag === 'ul' ? 'unordered_list' : 'ordered_list',
                'items' => $this->parseListItems($node),
                'id'    => $id,
                'class' => $class,
            ];
        }

        if (in_array($tag, ['section', 'div', 'article', 'header', 'footer', 'main', 'nav', 'aside'])) {
            return [
                'type'     => $tag === 'section' ? 'section' : 'block',
                'tag'      => $tag,
                'id'       => $id,
                'class'    => $class,
                'children' => $this->parseChildren($node),
            ];
        }

        return [
            'type'  => 'generic',
            'tag'   => $tag,
            'text'  => trim($node->textContent),
            'id'    => $id,
            'class' => $class,
        ];
    }

    private function parseChildren(DOMElement $node): array
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $children[] = $this->parseNode($child);
            }
        }
        return array_values(array_filter($children));
    }

    private function parseListItems(DOMElement $node): array
    {
        $items = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement && strtolower($child->nodeName) === 'li') {
                $items[] = trim($child->textContent);
            }
        }
        return $items;
    }

    private function innerHtml(DOMElement $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }
        return $html;
    }

    private function isCtaClass(string $class): bool
    {
        if (empty($class)) {
            return false;
        }
        $classes = preg_split('/\s+/', strtolower($class));
        foreach ($classes as $c) {
            foreach (self::CTA_CLASS_PATTERNS as $pattern) {
                if ($c === $pattern || str_contains($c, $pattern)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function loadDom(string $html): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        libxml_use_internal_errors(true);
        $wrapped = '<html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>';
        $dom->loadHTML($wrapped, LIBXML_NOERROR);
        libxml_clear_errors();
        return $dom;
    }
}