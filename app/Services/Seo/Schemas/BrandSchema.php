<?php

namespace App\Services\Seo\Schemas;

class BrandSchema extends BaseSchema
{
    public function __construct(
        protected string $name,
        protected string $url,
        protected ?string $logo = null
    ) {}

    public function toArray(): array
    {
        return $this->clean([
            '@type' => 'Brand',
            '@id' => $this->url . '#brand',
            'name' => $this->name,
            'url' => $this->url,
            'logo' => $this->logo,
        ]);
    }
}
