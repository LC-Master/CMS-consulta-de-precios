<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CenterMediaError extends Model
{
    use HasUuids;
    protected $fillable = [
        'center_id',
        'media_id',
        'name',
        'checksum',
        'error_count',
        'error_type',
        'last_seen_at',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id', 'id', 'media');
    }
}
