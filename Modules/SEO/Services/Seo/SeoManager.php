<?php

namespace Modules\SEO\Services\Seo;

use Modules\SEO\Services\Seo\Schemas\BaseSchema;
use Modules\SEO\Services\Seo\Schemas\OrganizationSchema;
use Modules\SEO\Services\Seo\Schemas\WebsiteSchema;

class SeoManager
{
    protected array $schemas = [];
    protected array $meta = [];
    protected ?OrganizationSchema $organization = null;
    protected ?WebsiteSchema $website = null;
    protected string $appName = 'ACTiV';

    public function __construct()
    {
        $this->addGlobalSchemas();
    }

    public function getAppName(): string
    {
        return $this->appName;
    }

    protected function addGlobalSchemas(): void
    {
        // Pull from settings table (seeded via LocalBusinessSettingsSeeder)
        $settings = \Modules\Settings\Models\Setting::whereIn('key', [
            'company_name',
            'company_logo',
            'social_facebook',
            'social_instagram',
            'social_linkedin',
            'social_twitter',
            'social_youtube'
        ])->pluck('value', 'key');

        $this->appName = $settings['company_name'] ?? config('app.name');
        $appName = $this->appName;
        $baseUrl = config('app.url');
        $logoPath = $settings['company_logo'] ?? 'assets/img/logo/logo.png';
        $logoUrl = str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath);

        // Social links for sameAs
        $socials = array_filter([
            $settings['social_facebook'] ?? null,
            $settings['social_instagram'] ?? null,
            $settings['social_linkedin'] ?? null,
            $settings['social_twitter'] ?? null,
            $settings['social_youtube'] ?? null,
        ]);

        // Organization
        $this->organization = new OrganizationSchema(
            $appName,
            $baseUrl,
            $logoUrl,
            $socials
        );

        // WebSite
        $this->website = new WebsiteSchema(
            $appName,
            $baseUrl,
            $this->getOrganizationId()
        );
    }

    public function addSchema(BaseSchema $schema): self
    {
        $this->schemas[] = $schema;
        return $this;
    }

    public function hasSchemas(): bool
    {
        return !empty($this->schemas);
    }

    public function setMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getGraph(): array
    {
        $graph = [];
        
        // Add globals first
        $graph[] = $this->organization->toArray();
        $graph[] = $this->website->toArray();

        // Add dynamically added schemas
        foreach ($this->schemas as $schema) {
            $graph[] = $schema->toArray();
        }

        return $graph;
    }

    public function render(): string
    {
        $output = [
            '@context' => 'https://schema.org',
            '@graph' => $this->getGraph()
        ];

        return '<script type="application/ld+json">' . json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }

    public function renderMeta(): string
    {
        $html = [];
        foreach ($this->meta as $m) {
            if (isset($m['property'])) {
                $html[] = sprintf('<meta property="%s" content="%s">', $m['property'], e($m['content']));
            } elseif (isset($m['name'])) {
                $html[] = sprintf('<meta name="%s" content="%s">', $m['name'], e($m['content']));
            } elseif (isset($m['rel'])) {
                $html[] = sprintf('<link rel="%s" href="%s">', $m['rel'], e($m['href']));
            }
        }

        return implode("\n    ", $html);
    }


    public function getOrganizationId(): string
    {
        return config('app.url') . '#organization';
    }

    public function getWebsiteId(): string
    {
        return config('app.url') . '#website';
    }
}
