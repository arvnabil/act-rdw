<?php

namespace App\Services\Seo;

use App\Services\Seo\Schemas\BaseSchema;
use App\Services\Seo\Schemas\OrganizationSchema;
use App\Services\Seo\Schemas\WebsiteSchema;

class SeoManager
{
    protected array $schemas = [];
    protected ?OrganizationSchema $organization = null;
    protected ?WebsiteSchema $website = null;

    public function __construct()
    {
        $this->initGlobalSchemas();
    }

    protected function initGlobalSchemas(): void
    {
        $baseUrl = config('app.url');
        $appName = config('app.name', 'ACTiV');

        // Singleton Organization
        $this->organization = new OrganizationSchema(
            name: $appName,
            url: $baseUrl,
            logo: asset('assets/img/logo/logo.png'),
            sameAs: [
                // Add social links from settings if available
            ]
        );

        // Singleton Website
        $this->website = new WebsiteSchema(
            name: $appName,
            url: $baseUrl,
            publisherId: $baseUrl . '#organization'
        );
    }

    public function addSchema(BaseSchema $schema): self
    {
        $this->schemas[] = $schema;
        return $this;
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


    public function getOrganizationId(): string
    {
        return config('app.url') . '#organization';
    }

    public function getWebsiteId(): string
    {
        return config('app.url') . '#website';
    }
}
