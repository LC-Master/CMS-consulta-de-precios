<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CentersModel;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\DeviceFactory;

#[UseFactory(DeviceFactory::class)]
class DevicesModel extends Model
{
    use HasFactory;
    protected $table = "devices";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function centers(){

        return $this->belongsTo(CentersModel::class, 'center_id','id');
    }
}
