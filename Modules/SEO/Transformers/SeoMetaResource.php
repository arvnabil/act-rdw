<?php

namespace Modules\SEO\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;

class SeoMetaResource extends JsonApiResource
{
    /**
     * Get the resource's ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toId(Request $request): string
    {
        return (string) $this->id;
    }

    /**
     * Get the resource's type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toType(Request $request): string
    {
        return 'seo';
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image_url' => $this->og_image ? url('storage/' . $this->og_image) : null,
            'canonical_url' => $this->canonical_url,
            'noindex' => (bool) $this->noindex,
        ];
    }
}
