<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model
{
    /** @use HasFactory<\Database\Factories\AgreementFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = ['name','legal_name','tax_id',
    'contact_person','contact_email','contact_phone','start_date',
    'end_date','is_active','observations','created_at','updated_at'];
}
