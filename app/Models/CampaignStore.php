<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignStore extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignStoreFactory> */
    use HasFactory;

    use HasUuids;

    public function campaign()
    {

        return $this->belongsTo(Campaign::class);
    }

    public function center()
    {

        return $this->belongsTo(Center::class);
    }
}
