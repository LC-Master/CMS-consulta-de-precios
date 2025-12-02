<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignsModel;

class CampaignContentModel extends Model
{
    protected $table = "campaign_content";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function campaign(){

        return $this->belongsTo(CampaignsModel::class, 'campaign_id','id');
    }

}
