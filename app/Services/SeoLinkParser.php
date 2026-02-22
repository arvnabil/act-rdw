<?php

namespace App\Services;

use App\Models\SeoWhitelistDomain;
use Illuminate\Support\Str;

class SeoLinkParser
{
    protected string $content;
    protected array $whitelist;
    protected string $appHost;

    public function __construct(string $content)
    {
        $this->content = $content;
        $this->appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? '';
        $this->whitelist = $this->getCachedWhitelist();
    }

    public static function parse(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        return (new self($html))->execute();
    }

    public static function getRelAttribute(?string $url): string
    {
        if (empty($url) || Str::startsWith($url, ['#', 'mailto:', 'tel:', '/', './', '../'])) {
            return 'noopener noreferrer';
        }

        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? '';
        $host = parse_url($url, PHP_URL_HOST);

        if (!$host || $host === $appHost) {
            return 'noopener noreferrer';
        }

        $instance = new self('');
        $rel = ['noopener', 'noreferrer'];

        if (!$instance->isWhitelisted($host)) {
            $rel[] = 'nofollow';
        }

        return implode(' ', $rel);
    }

    protected function execute(): string
    {
        if (!Str::contains($this->content, '<a')) {
            return $this->content;
        }

        $dom = new \DOMDocument();
        
        // Handle UTF-8 by converting to HTML entities
        $html = mb_convert_encoding($this->content, 'HTML-ENTITIES', 'UTF-8');
        
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $links = $dom->getElementsByTagName('a');

        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            if (empty($href) || Str::startsWith($href, ['#', 'mailto:', 'tel:', '/', './', '../'])) {
                continue;
            }

            $host = parse_url($href, PHP_URL_HOST);

            if (!$host || $host === $this->appHost) {
                continue;
            }

            // At this point, it's an external link
            $this->processExternalLink($link, $host);
        }

        return trim($dom->saveHTML());
    }

    protected function processExternalLink(\DOMElement $link, string $host): void
    {
        // 1. Force open in new tab
        $link->setAttribute('target', '_blank');

        // 2. Base rel attributes for security
        $rel = ['noopener', 'noreferrer'];

        // 3. Check if domain is NOT whitelisted
        if (!$this->isWhitelisted($host)) {
            $rel[] = 'nofollow';
        }

        $link->setAttribute('rel', implode(' ', $rel));
    }

    protected function isWhitelisted(string $host): bool
    {
        foreach ($this->whitelist as $domain) {
            // Match exact domain or subdomains (e.g. wikipedia.org matches en.wikipedia.org)
            if ($host === $domain || Str::endsWith($host, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }

    protected function getCachedWhitelist(): array
    {
        // In a real production app, we should cache this for performance
        return SeoWhitelistDomain::where('is_active', true)
            ->pluck('domain')
            ->toArray();
    }
}
