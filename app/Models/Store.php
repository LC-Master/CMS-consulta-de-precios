<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\StoreSyncState;

class Store extends Model
{
    use HasApiTokens;

    protected $table = 'Store';
    protected $primaryKey = 'ID';
    protected $keyType = 'integer';
    public $timestamps = false;
    public $incrementing = false;

    protected $appends = [
        'id',
        'name',
        'store_code',
        'address',
        'region',
        'city',
        'country',
        'fax_number',
        'phone_number',
        // 'inactive',
    ];
    // protected $fillable = [
    //     "Region",
    //     "City",
    //     "Country",
    //     "FaxNumber",
    //     "PhoneNumber",
    //     "Inactive"
    // ];

    protected $hidden = [
        "Region",
        "City",
        "Country",
        "FaxNumber",
        "PhoneNumber",
        "Inactive",
        "Address1",
        "Zip",
        "pivot",
        "ID",
        "Name",
        "StoreCode",
        "State",
        "Inactive",
        "Address2",
        "ParentStoreID",
        "ScheduleHourMask1",
        "ScheduleHourMask2",
        "ScheduleHourMask3",
        "ScheduleHourMask4",
        "ScheduleHourMask5",
        "ScheduleHourMask6",
        "ScheduleHourMask7",
        "ScheduleMinute",
        "RetryCount",
        "RetryDelay",
        "LastUpdated",
        "DBTimeStamp",
        "AccountName",
        "Password",
        "AutoID",
        "SyncedStoreStatus",
        "SyncGuid",
        "StoreKey"
    ];

    protected $casts = [
        'ID' => 'integer',
        'Inactive' => 'boolean',
    ];

    /**
     * Accessor para el ID
     * Permite usar $store->id en lugar de $store->ID
     */
    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['ID'] ?? null,
        );
    }

    /**
     * Accessor para el Name
     * Permite usar $store->name en lugar de $store->Name
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['Name'] ?? 'Sin nombre',
        );
    }
    /**
     * Summary of address
     * @return Attribute
     * Accessor para el Address1 recortado antes del guion
     *  Permite usar $store->address en lugar de $store->Address1
     */
    protected function address(): Attribute
    {
        $address1 = $this->attributes['Address1'] ?? '';

        return Attribute::make(
            get: fn() => $address1,
        );
    }

    /**
     * Accessor para el StoreCode (Snake Case)
     * Permite usar $store->store_code en lugar de $store->StoreCode
     */
    protected function storeCode(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['StoreCode'] ?? null,
        );
    }

    /**
     * Accessor para el Region
     * Permite usar $store->region en lugar de $store->Region
     */
    protected function region(): Attribute
    {
        return Attribute::make(
            get: fn() => explode('-', $this->attributes['Region'] ?? '')[1] ?? null,
        );
    }

    /**
     * Accessor para el City
     * Permite usar $store->city en lugar de $store->City
     */
    protected function city(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['City'] ?? null,
        );
    }

    /**
     * Accessor para el Country
     * Permite usar $store->country en lugar de $store->Country
     */
    protected function country(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['Country'] ?? null,
        );
    }

    /**
     * Accessor para el FaxNumber
     * Permite usar $store->fax_number en lugar de $store->FaxNumber
     */
    protected function faxNumber(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['FaxNumber'] ?? null,
        );
    }

    /**
     * Accessor para el PhoneNumber
     * Permite usar $store->phone_number en lugar de $store->PhoneNumber
     */
    protected function phoneNumber(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['PhoneNumber'] ?? null,
        );
    }

    // /**
    //  * Accessor para el Inactive
    //  * Permite usar $store->inactive en lugar de $store->Inactive
    //  */
    // protected function inactive(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn() => $this->attributes['Inactive'] ?? null,
    //     );
    // }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('Inactive', fn($q) => $q->where('Inactive', false));
    }

    protected function castAttribute($key, $value)
    {
        if (\is_string($value)) {
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return parent::castAttribute($key, $value);
    }
    protected function sushiShouldBeUtf8($value)
    {
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }

    public function jsonSerialize(): mixed
    {
        $array = parent::toArray();
        return array_map(fn($value) => \is_string($value) ? mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1') : $value, $array);
    }
    public static function getStoresByGroup()
    {
        return static::query()
            ->get()
            ->groupBy('Region')
            ->map(fn($stores, $region) => [
                'region' => $region,
                'stores' => $stores->map(fn($store) => [
                    'id' => $store->getKey(),
                    'name' => $store->getAttribute('Name'),
                    'store_code' => $store->getAttribute('StoreCode'),
                    'address' => trim("{$store->getAttribute('Address1')}, {$store->getAttribute('City')}, {$store->getAttribute('State')} {$store->getAttribute('Zip')}, {$store->getAttribute('Country')}"),
                    'phone_number' => $store->getAttribute('PhoneNumber'),
                    'fax_number' => $store->getAttribute('FaxNumber'),
                ])->values(),
            ])->values();
    }
    public function syncState()
    {
        return $this->hasOne(StoreSyncState::class, 'store_id', 'ID');
    }
    public function centerMediaErrors()
    {
        return $this->hasMany(CenterMediaError::class, 'store_id', 'ID');
    }
}
