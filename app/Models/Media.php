<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'disk',
        'path',
        'mime_type',
        'size',
        'duration_seconds',
        'checksum',
        'created_by',
    ];
    public function timeLineItems()
    {
        return $this->hasMany(TimeLineItem::class);
    }
    public function thumbnail()
    {
        return $this->hasOne(Thumbnail::class);
    }
    public function campaigns()
    {
        return $this->belongsToMany(
            Campaign::class,
            'time_line_items',
            'media_id',
            'campaign_id'
        );
    }
}
