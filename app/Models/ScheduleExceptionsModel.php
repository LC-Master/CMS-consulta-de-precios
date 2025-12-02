<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignsModel;

class ScheduleExceptionsModel extends Model
{
    protected $table = "schedule_exceptions";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function campaign(){

        return $this->belongsTo(CampaignsModel::class, 'campaign_id','id');
    }
}
