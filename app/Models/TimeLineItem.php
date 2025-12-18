<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Campaign;
use App\Models\Media;

class TimeLineItem extends Model
{
    /** @use HasFactory<\Database\Factories\TimeLineItemFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'campaign_id',
        'media_id',
        'slot',
        'position',
    ];
    public function media()
    {

        return $this->belongsTo(Media::class, 'media_id', 'id');

    }

    public function campaign()
    {

        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');

    }
}
