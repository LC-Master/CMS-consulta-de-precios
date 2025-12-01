<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignsModel;
use App\Models\CentersModel;

class CampaignStoresModel extends Model
{
    protected $table = "campaign_stores";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function campaign(){

        return $this->belongsTo(CampaignsModel::class, 'campaign_id','id');
    }
    
    public function center(){

        return $this->belongsTo(CentersModel::class, 'center_id','id');
    }
}
