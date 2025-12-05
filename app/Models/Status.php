<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /** @use HasFactory<\Database\Factories\StatusFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'status',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
}
