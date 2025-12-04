<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignContent extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignContentFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'campaign_id',
        'campaign_type',
        'url',
        'metadata',
    ];

    public function campaign()
    {

        return $this->belongsTo(Campaign::class);
    }
}
