<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImageCleanup;

class Setting extends Model
{
    use HasImageCleanup;

    protected $guarded = [];

    public function getCleanupFields(): array
    {
        $imageKeys = ['seo_favicon', 'seo_default_og_image'];

        if (in_array($this->key, $imageKeys)) {
            return ['value'];
        }

        return [];
    }
}
