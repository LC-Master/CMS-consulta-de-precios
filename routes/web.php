<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ThumbnailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CenterController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', fn() => redirect()->route('campaign.index'))
        ->name('home')
        ->middleware('permission:campaign.list');

    Route::get('/centers', [CenterController::class, 'index'])
        ->name('centers.index')
        ->middleware('permission:center.list');

    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    Route::resource('media', MediaController::class)->parameters([
        'media' => 'media'
    ]);

    Route::get('/media/cdn/{media}', [MediaController::class, 'preview'])
        ->name('media.cdn');

    Route::post('/media/upload', [MediaController::class, 'store'])
        ->name('media.upload')
        ->middleware('permission:media.upload');

    Route::get('logs', [ActivityLogController::class, 'index'])
        ->name('logs.index')
        ->middleware('permission:log.list');

    Route::resource('agreement', AgreementController::class);

    Route::get('thumbnail/cdn/{thumbnail}', [ThumbnailController::class, 'show'])
        ->name('thumbnail.cdn');
});

Route::get(
    '/health',
    fn() =>
    response()->json(['status' => 'OK'], 200)
);

require __DIR__ . '/campaign.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/settings.php';
