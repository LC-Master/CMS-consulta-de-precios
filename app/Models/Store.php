<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

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
    ];
    protected $fillable = [
        "Region",
        "Address1",
        "City",
        "Country",
        "FaxNumber",
        "PhoneNumber",
        "Inactive"
    ];

    protected $hidden = [
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
     * Accessor para el StoreCode (Snake Case)
     * Permite usar $store->store_code en lugar de $store->StoreCode
     */
    protected function storeCode(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['StoreCode'] ?? null,
        );
    }

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
}
