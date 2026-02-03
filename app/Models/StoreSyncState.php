<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSyncState extends Model
{
    protected $fillable = [
        'store_id',
        'url',
        'placeholder_id',
        'is_syncing',
        'sync_started_at',
        'sync_ended_at',
        'disk',
        'uptimed_at',
        'last_sync_status',
        'last_synced_at',
        'last_reported_at',
    ];
    protected $casts = [
        'is_syncing' => 'boolean',
        'disk' => 'array',
        'sync_started_at' => 'datetime',
        'sync_ended_at' => 'datetime',
        'uptimed_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'last_reported_at' => 'datetime',
    ];
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'ID');
    }
    public function placeholder()
    {
        return $this->belongsTo(Media::class, 'placeholder_id');
    }
}
