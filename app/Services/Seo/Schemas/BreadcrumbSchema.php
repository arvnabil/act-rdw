<?php

namespace App\Services\Seo\Schemas;

class BreadcrumbSchema extends BaseSchema
{
    public function __construct(
        protected array $breadcrumbs // [['name' => '...", 'item' => '...']]
    ) {}

    public function toArray(): array
    {
        $items = [];
        foreach ($this->breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['item'],
            ];
        }

        return $this->clean([
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ]);
    }
}
