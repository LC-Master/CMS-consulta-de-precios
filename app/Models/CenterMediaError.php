<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;

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

    public function store()
    {
        return $this->belongsToMany(Store::class, 'store_id', 'ID');
    }
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
