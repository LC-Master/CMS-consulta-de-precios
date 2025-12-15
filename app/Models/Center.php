<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Center extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\CenterFactory> */
    use HasFactory;
    use HasUuids;
    use HasApiTokens;
    protected $fillable = [
        'name',
        'code',
    ];
}
