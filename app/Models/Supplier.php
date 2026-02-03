<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Agreement;

class Supplier extends Model
{
    use HasUuids;

    protected $table = 'Supplier';
    
    protected $primaryKey = 'id'; 
    public $incrementing = false;
    protected $keyType = 'string';

    const UPDATED_AT = 'LastUpdated';
    const CREATED_AT = null;

    protected $fillable = [
        'Country', 'HQID', 'LastUpdated', 'State', 
        'ID',
        'SupplierName', 'ContactName', 'Address1', 'Address2', 'City', 'Zip', 
        'EmailAddress', 'WebPageAddress', 'Code', 'AccountNumber', 
        'TaxNumber', 'CurrencyID', 'PhoneNumber', 'FaxNumber', 
        'CustomText1', 'CustomText2', 'Notes', 'Terms', 'SyncGuid'
    ];

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'supplier_id', 'ID');
    }
}