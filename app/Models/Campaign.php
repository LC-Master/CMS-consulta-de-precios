<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
    use HasFactory;

    protected $fillable = [
        'campaign_name',
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
            // Set (Mutador): Lo que entra se formatea para SQL Server antes de guardar
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

    public function department()
    {
        return $this->belongsTo(DepartmentsModel::class, 'department_id', 'id');
    }

    public function agreement()
    {
        return $this->belongsTo(AgreementsModel::class, 'agreement_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(StatusModel::class, 'status_id', 'id');
    }
}
