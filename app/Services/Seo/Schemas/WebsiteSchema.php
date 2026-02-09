<?php

namespace App\Services\Seo\Schemas;

class WebsiteSchema extends BaseSchema
{
    public function __construct(
        protected string $name,
        protected string $url,
        protected string $publisherId
    ) {}

    public function toArray(): array
    {
        return $this->clean([
            '@type' => 'WebSite',
            '@id' => $this->qualifyId('#website', $this->url),
            'url' => $this->url,
            'name' => $this->name,
            'publisher' => ['@id' => $this->publisherId],
            'inLanguage' => 'id-ID',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => rtrim($this->url, '/') . '/search?q={search_term_string}'
                ],
                'query-input' => 'required name=search_term_string'
            ]
        ]);
    }
}
