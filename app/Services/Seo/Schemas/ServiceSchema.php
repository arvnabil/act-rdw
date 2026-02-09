<?php

namespace App\Services\Seo\Schemas;

class ServiceSchema extends BaseSchema
{
    public function __construct(
        protected string $name,
        protected string $description,
        protected string $url,
        protected ?string $image,
        protected array $provider, // ['@id' => '...']
        protected array $offers = [] // Dynamic offers/solutions
    ) {}

    public function toArray(): array
    {
        return $this->clean([
            '@type' => 'Service',
            '@id' => $this->url . '#service',
            'name' => $this->name,
            'description' => $this->description,
            'url' => $this->url,
            'image' => $this->image,
            'provider' => $this->provider,
            'hasOfferCatalog' => !empty($this->offers) ? [
                '@type' => 'OfferCatalog',
                'name' => "{$this->name} Solutions",
                'itemListElement' => array_map(fn($offer) => [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name' => $offer['name'],
                        'description' => $offer['description'] ?? null
                    ]
                ], $this->offers)
            ] : null,
            'mainEntityOfPage' => ['@id' => $this->url . '#webpage'],
        ]);
    }
}
