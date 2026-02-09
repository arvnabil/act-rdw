<?php

namespace App\Services\Seo\Schemas;

class OrganizationSchema extends BaseSchema
{
    public function __construct(
        protected string $name,
        protected string $url,
        protected ?string $logo = null,
        protected array $sameAs = []
    ) {}

    public function toArray(): array
    {
        return $this->clean([
            '@type' => 'Organization',
            '@id' => $this->qualifyId('#organization', $this->url),
            'name' => $this->name,
            'url' => $this->url,
            'logo' => $this->logo ? [
                '@type' => 'ImageObject',
                'url' => $this->logo,
                'width' => 512,
                'height' => 512,
            ] : null,
            'sameAs' => $this->sameAs,
        ]);
    }
}
