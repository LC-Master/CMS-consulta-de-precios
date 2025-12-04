<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory;

    protected $fillable = [
        'disk',
        'path',
        'mime_type',
        'size',
        'duration_seconds',
        'checksum',
        'created_by',
    ];
}
