<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TimeLineItem;
use App\Models\Media;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read \App\Models\User $user
 */
class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    public $old_media_files;
    public $old_agreements;
    public $old_centers;

    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'status_id',
        'department_id',
        'user_id',
        'updated_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * MUTADOR para start_at
     * Se ejecuta automáticamente al hacer $model->start_at = 'valor';
     */
    protected static function booted()
    {
        static::creating(function ($campaign) {
            if (Auth::check() && empty($campaign->user_id)) {
                $campaign->user_id = Auth::id();
            }
        });
        static::updating(function ($campaign) {
            if (Auth::check()) {
                $campaign->updated_by = Auth::id();
            }
        });
        static::deleting(function ($campaign) {
            if (Auth::check()) {
                $campaign->updated_by = Auth::id();
                $campaign->save();
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function startAt(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
        );
    }

    /**
     * MUTADOR para end_at
     */
    protected function endAt(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
        );
    }
    public function timeLineItems()
    {
        return $this->hasMany(TimeLineItem::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function agreements()
    {
        return $this->belongsToMany(Agreement::class, 'campaign_agreements')->withTimestamps();
    }

    public function Stores()
    {
        return $this->belongsToMany(Store::class, 'campaign_store', 'campaign_id', 'store_id')->withTimestamps();
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class, 'campaign_centers')->withTimestamps();
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function media()
    {
        return $this->belongsToMany(Media::class, 'time_line_items', 'campaign_id', 'media_id')->withPivot('slot', 'position')
            ->withTimestamps();
    }
}
