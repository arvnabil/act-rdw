<?php

namespace Modules\Campaign\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignCta extends Model
{
    protected $table = 'campaign_ctas';

    protected $guarded = ['id'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}