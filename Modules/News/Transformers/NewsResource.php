<?php

namespace Modules\News\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\SEO\Transformers\SeoMetaResource;

class NewsResource extends JsonApiResource
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
        return 'news';
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
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'thumbnail_url' => $this->thumbnail ? url('storage/' . $this->thumbnail) : null,
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
            'categories' => NewsCategoryResource::class,
            'tags' => NewsTagResource::class,
            'author' => UserResource::class,
            'seo' => SeoMetaResource::class,
        ];
    }
}
