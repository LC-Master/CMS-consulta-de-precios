<?php

namespace App\Models;

use App\Traits\FixSqlServerDates;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CenterSnapshot extends Model
{

    use HasUuids, FixSqlServerDates;
    protected $fillable = [
        'store_id',
        'snapshot_json',
        'version_hash',
    ];
    /**
     * Mutador para snapshot_json:
     * - Convierte el array a JSON
     * - Genera automáticamente el hash y lo guarda en version_hash
     */
    public function setSnapshotJsonAttribute($value)
    {
        $json = json_encode($value);
        $this->attributes['snapshot_json'] = $json;

        $this->attributes['version_hash'] = hash('sha256', $json);
    }

    /**
     * Accessor para convertir JSON a array al leer
     */
    public function getSnapshotJsonAttribute($value)
    {
        return json_decode($value, true);
    }
}
