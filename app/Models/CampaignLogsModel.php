<?php

namespace App\Models;

use Database\Factories\CampaignLogFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignsModel;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[UseFactory(CampaignLogFactory::class)]
class CampaignLogsModel extends Model
{
    use HasFactory;
    protected $table = "campaign_logs";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function campaign(){

        return $this->belongsTo(CampaignsModel::class, 'campaign_id','id');
    }
}
