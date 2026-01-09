<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thumbnail extends Model
{
    /** @use HasFactory<\Database\Factories\ThumbnailFactory> */
    use HasFactory,HasUuids;

    protected $fillable = [
        'path',
        'media_id',
        'name',
        'mime_type',
        'size',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
