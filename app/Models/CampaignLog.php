<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignLog extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignLogFactory> */
    use HasFactory;
    use HasUuids;

    
    public function campaign(){
        return $this->belongsTo(Campaign::class);
    }
}
