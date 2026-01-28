<?php

//**
//  * Configuration file for log retention periods.
//  * @return array
//  */

return [
    'soft_delete_old_logs' => env('SOFT_DELETE_OLD_LOGS', 3),
    'force_delete_old_logs' => env('FORCE_DELETE_OLD_LOGS', 6),
];