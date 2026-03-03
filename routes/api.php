<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CenterSnapshotController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/store/snapshot', [CenterSnapshotController::class, 'show']);
    Route::post('/store/health', [CenterSnapshotController::class, 'health']);
    Route::get('/media/{media}', [CenterSnapshotController::class, 'download']);
});
