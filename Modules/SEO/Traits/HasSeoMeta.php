<?php

namespace Modules\SEO\Traits;

use Modules\SEO\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeoMeta
{
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
