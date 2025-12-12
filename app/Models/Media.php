<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'disk',
        'path',
        'mime_type',
        'size',
        'duration_seconds',
        'checksum',
        'created_by',
    ];
}
