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

    public $timestamps = false; 

    protected $fillable = [
        'campaign_id', 'user_id', 'action', 'level', 
        'message', 'properties', 'ip_address', 
        'user_agent', 'referer'
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function campaign(){
        return $this->belongsTo(Campaign::class);
    }
}
