<?php

namespace App\Handler\Media;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Exceptions\MediaInUseException;

class MediaSafeAction
{
    /**
     * Create a new class instance.
     */
    public static function MediaSafeAction(callable $callback)
    {
        try {
            return $callback();
        } catch (MediaInUseException $e) {
            throw $e;
        } catch (QueryException $e) {
            Log::error("Error de base de datos en Media: " . $e->getMessage());
            return back()->with('error', 'Error de integridad: No se pudo completar la operación en la base de datos.');
        } catch (\Throwable $e) {
            Log::critical("Fallo inesperado en Media: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al procesar los archivos.');
        }
    }
}
