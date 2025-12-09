<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Campaign;
use App\Models\Center;

class CampaignCenter extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignCenterFactory> */
    use HasFactory,HasUuids;

    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }

    public function center() {
        return $this->belongsTo(Center::class);
    }
}
