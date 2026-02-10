<?php

namespace App\Traits;
use Carbon\Carbon;

trait FixSqlServerDates
{
    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }

    public function setEndAtAttribute($value)
    {
        $this->attributes['end_at'] = $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }
    public function getDateFormat()
    {
        // Esto es el estándar ANSI que SQL Server NUNCA rechaza
        return 'Y-m-d H:i:s';
    }
    /**
     * Override the default date serialization format to ensure compatibility with SQL Server.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        // Formato plano: YYYY-MM-DD HH:mm:ss
        return $date->format('Y-m-d H:i:s');
    }
}