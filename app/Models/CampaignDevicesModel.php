<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignsModel;
use App\Models\DevicesModel;

class CampaignDevicesModel extends Model
{
    protected $table = "campaign_devices";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function campaign(){

        return $this->belongsTo(CampaignsModel::class, 'campaign_id','id');
    }

    public function devices(){

        return $this->belongsTo(DevicesModel::class, 'device_id','id');
    }
}
