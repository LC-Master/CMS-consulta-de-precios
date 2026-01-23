<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ActivityLog extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityLogFactory> */
    use HasFactory;

    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'subject_id',
        'subject_type',
        'user_name',
        'user_email',
        'causer_id',
        'action',
        'level',
        'message',
        'properties',
        'ip_address',
        'user_agent',
        'referer'
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Relación con el usuario que realizó la acción
     */
    public function user(): BelongsTo
    {
        // Esto asume que tu modelo de usuario es App\Models\User
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica con el objeto afectado (Campaign, User, Token)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
