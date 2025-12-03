<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CenterFactory;
use App\Models\DevicesModel;

#[UseFactory(CenterFactory::class)]

class CentersModel extends Model
{
    use HasFactory;
    protected $table = "centers";

    protected $primaryKey = "id";

    public $timestamps = true;

}
