<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\StatusFactory;

#[UseFactory(StatusFactory::class)]
class StatusModel extends Model
{
    use HasFactory;
    protected $table = "status";

    protected $primaryKey = "id";

    protected $fillable = [
        'status_name',
    ];

    public $timestamps = true;

}
