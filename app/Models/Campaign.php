<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TimeLineItem;

class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'status_id',
        'department_id',
        'agreement_id',
        'created_by',
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
    protected function startAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
        );
    }

    /**
     * MUTADOR para end_at
     */
    protected function endAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
        );
    }
    public function timeLineItems(){
        return $this->belongsToMany(TimeLineItem::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class, 'campaign_centers')->withTimestamps();
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
