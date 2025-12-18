<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
}
