<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterSnapshot extends Model
{
    protected $fillable = [
        'center_id',
        'snapshot_json',
        'version_hash',
    ];
}
