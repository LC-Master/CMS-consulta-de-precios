<?php

namespace App\Enums\Log;

enum LogLevelEnum: string
{
    case SUCCESS = 'success'; 
    case INFO = 'info';   
    case WARNING = 'warning'; 
    case DANGER = 'danger';
}
