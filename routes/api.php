<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CenterSnapshotController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/centers/snapshots', [CenterSnapshotController::class, 'show']);
});
