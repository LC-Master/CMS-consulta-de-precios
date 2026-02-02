<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Supplier;

class Agreement extends Model
{
    /** @use HasFactory<\Database\Factories\AgreementFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'contact_person',
        'contact_email',
        'contact_phone',
        'is_active',
        'observations',
        'supplier_id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['pivot'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}