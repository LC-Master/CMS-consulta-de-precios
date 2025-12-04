<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
<<<<<<< HEAD:app/Models/Campaign.php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
    use HasFactory;
    use HasUuids;
=======
use Carbon\Carbon; 
use App\Models\DepartmentsModel;
use App\Models\AgreementsModel;
use App\Models\StatusModel;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CampaignFactory;

#[UseFactory(CampaignFactory::class)]
class CampaignsModel extends Model
{
    use HasFactory;
    protected $table = "campaigns";

    protected $primaryKey = "id";

    public $timestamps = true;

>>>>>>> main:app/Models/CampaignsModel.php
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
        return $this->belongsTo(Department::class);
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }


    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
