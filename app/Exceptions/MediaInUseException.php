<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
class MediaInUseException extends Exception
{
    protected $message = 'El archivo está siendo usado en el sistema y no puede ser borrado.';
}
