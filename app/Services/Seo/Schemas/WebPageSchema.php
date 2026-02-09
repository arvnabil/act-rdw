<?php

namespace App\Services\Seo\Schemas;

class WebPageSchema extends BaseSchema
{
    public function __construct(
        protected string $title,
        protected string $description,
        protected string $url,
        protected string $websiteId,
        protected ?string $mainEntityId = null
    ) {}

    public function toArray(): array
    {
        return $this->clean([
            '@type' => 'WebPage',
            '@id' => $this->url . '#webpage',
            'url' => $this->url,
            'name' => $this->title,
            'description' => $this->description,
            'isPartOf' => ['@id' => $this->websiteId],
            'inLanguage' => 'id-ID',
            'mainEntity' => $this->mainEntityId ? ['@id' => $this->mainEntityId] : null,
        ]);
    }
}
