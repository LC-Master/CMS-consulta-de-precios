<?php

namespace App\Enums;

enum SyncStatusEnum: string
{
    case PENDING = 'pending';
    case SYNCING = 'syncing';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case STALE = 'stale';
}
