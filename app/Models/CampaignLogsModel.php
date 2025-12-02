<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignsModel;

class CampaignLogsModel extends Model
{
    protected $table = "campaign_logs";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function campaign(){

        return $this->belongsTo(CampaignsModel::class, 'campaign_id','id');
    }
}
