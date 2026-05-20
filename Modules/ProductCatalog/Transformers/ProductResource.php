<?php

namespace Modules\ProductCatalog\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Services\Transformers\ServiceResource;
use Modules\Services\Transformers\ServiceSolutionResource;
use Modules\SEO\Transformers\SeoMetaResource;

class ProductResource extends JsonApiResource
{
    /**
     * Indicate that relationship loading should automatically rely on previously loaded relationships.
     *
     * @var bool
     */
    protected bool $includesPreviouslyLoadedRelationships = true;

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
        return 'products';
    }

    /**
     * Transform the resource's attributes into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'description' => $this->description,
            'image_url' => $this->image_path ? url('storage/' . $this->image_path) : null,
            'datasheet_url' => $this->datasheet_url,
            'specs' => $this->specs,
            'features' => $this->features,
            'tags' => $this->tags,
            'specification_text' => $this->specification_text,
            'features_text' => $this->features_text,
            'link_accommerce' => $this->link_accommerce,
            'whatsapp_note' => $this->whatsapp_note,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toRelationships(Request $request): array
    {
        return [
            'brand' => BrandResource::class,
            'service' => ServiceResource::class,
            'categories' => ProductCategoryResource::class,
            'solutions' => ServiceSolutionResource::class,
            'seo' => SeoMetaResource::class,
        ];
    }
}
