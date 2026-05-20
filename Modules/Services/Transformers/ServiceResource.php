<?php

namespace Modules\Services\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;

class ServiceResource extends JsonApiResource
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
        return 'services';
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
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
