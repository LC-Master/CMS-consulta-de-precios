<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CentersModel;

class DevicesModel extends Model
{
    protected $table = "devices";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function center(){

        return $this->belongsTo(CentersModel::class, 'center_id','id');
    }
}
