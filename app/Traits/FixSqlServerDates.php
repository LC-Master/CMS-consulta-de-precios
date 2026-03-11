<?php

namespace App\Traits;

trait FixSqlServerDates
{
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
        // Se elimina la 'Z' (UTC) para que el frontend respete la hora local exacta guardada.
        return $date->format('Y-m-d\TH:i:s');
    }
}