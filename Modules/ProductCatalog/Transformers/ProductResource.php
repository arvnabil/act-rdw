<?php

namespace Modules\ProductCatalog\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
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
            
            'brand' => $this->whenLoaded('brand', function() {
                return $this->brand ? ['id' => $this->brand->id, 'name' => $this->brand->name, 'slug' => $this->brand->slug] : null;
            }),
            'service' => $this->whenLoaded('service', function() {
                return $this->service ? ['id' => $this->service->id, 'name' => $this->service->name, 'slug' => $this->service->slug] : null;
            }),
            'categories' => $this->whenLoaded('categories', function() {
                return $this->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug]);
            }),
            'solutions' => $this->whenLoaded('solutions', function() {
                return $this->solutions->map(fn($s) => ['id' => $s->id, 'title' => $s->title, 'slug' => $s->slug ?? \Illuminate\Support\Str::slug($s->title)]);
            }),
            
            'link_accommerce' => $this->link_accommerce,
            'whatsapp_note' => $this->whatsapp_note,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            
            // SEO Meta data
            'seo_title' => $this->whenLoaded('seo', fn() => $this->seo->title ?? null),
            'seo_description' => $this->whenLoaded('seo', fn() => $this->seo->description ?? null),
            'seo_keywords' => $this->whenLoaded('seo', fn() => $this->seo->keywords ?? null),
            'og_title' => $this->whenLoaded('seo', fn() => $this->seo->og_title ?? null),
            'og_description' => $this->whenLoaded('seo', fn() => $this->seo->og_description ?? null),
            'og_image_url' => $this->whenLoaded('seo', fn() => ($this->seo && $this->seo->og_image) ? url('storage/' . $this->seo->og_image) : null),
            'canonical_url' => $this->whenLoaded('seo', fn() => $this->seo->canonical_url ?? null),
            'noindex' => $this->whenLoaded('seo', fn() => $this->seo->noindex ?? false),
        ];
    }
}
