<?php

namespace App\Models;

use Database\Factories\CampaignStoreFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignsModel;
use App\Models\CentersModel;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[UseFactory(CampaignStoreFactory::class)]

class CampaignStoresModel extends Model
{
    use HasFactory;
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
