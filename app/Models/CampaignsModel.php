<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DepartmentsModel;
use App\Models\AgreementsModel;
use App\Models\StatusModel;

class CampaignsModel extends Model
{
    protected $table = "campaigns";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function department(){

        return $this->belongsTo(DepartmentsModel::class, 'department_id','id');
    }

    public function agreement(){

        return $this->belongsTo(AgreementsModel::class, 'agreement_id','id');
    }

    public function status(){

        return $this->belongsTo(StatusModel::class, 'status_id','id');
    }
    
}
