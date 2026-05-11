<?php

namespace Modules\News\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'thumbnail_url' => $this->thumbnail ? url('storage/' . $this->thumbnail) : null,
            
            'categories' => $this->whenLoaded('categories', function() {
                return $this->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug]);
            }),
            'tags' => $this->whenLoaded('tags', function() {
                return $this->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]);
            }),
            'author' => $this->whenLoaded('author', function() {
                return $this->author ? ['id' => $this->author->id, 'name' => $this->author->name] : null;
            }),
            
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
