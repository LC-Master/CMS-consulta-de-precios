<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ThumbnailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;

Route::middleware(['auth', 'verified', 'role:admin|publicidad'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('media', MediaController::class)->parameters([
        'media' => 'media'
    ]);
    Route::get('/media/cdn/{media}', [MediaController::class, 'preview']);
    
    Route::resource('logs', ActivityLogController::class)->only(['index']);
    // Route::resource('timeline', TimeLineController::class);
    Route::resource('agreement', AgreementController::class);
    Route::post('/media/upload', [MediaController::class, 'store'])->name('video.upload');
    Route::get('thumbnail/cdn/{thumbnail}', [ThumbnailController::class, 'show']);
    

});

require __DIR__ . '/campaign.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/settings.php';