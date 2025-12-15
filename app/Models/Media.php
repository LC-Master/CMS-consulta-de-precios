<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Campaign;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'disk',
        'path',
        'name',
        'mime_type',
        'size',
        'duration_seconds',
        'checksum',
        'created_by',
    ];

    public function campaigns()
    {
        // Relación Muchos a Muchos usando la tabla time_line_items
        return $this->belongsToMany(Campaign::class, 'time_line_items', 'media_id', 'campaign_id')
                    ->withTimestamps(); 
    }
}
