<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Device extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'center_id',
        'name',
        'type',
        'description',
        'is_active',
    ];

    public function center(){

        return $this->belongsTo(Center::class);
    }
}
