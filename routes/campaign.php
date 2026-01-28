<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignHistoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/campaign/activate/{campaign}', [CampaignController::class, 'activate'])
        ->name('campaign.activate')
        ->middleware('permission:campaign.activate');

    Route::get('/campaign/cancel/{campaign}', [CampaignController::class, 'cancel'])
        ->name('campaign.cancel')
        ->middleware('permission:campaign.cancel');

    Route::get('/campaigns/report', [CampaignController::class, 'report'])
        ->name('campaign.report.list')
        ->middleware('permission:campaign.report');
        
    Route::get('/campaigns/export', [CampaignController::class, 'export'])
        ->name('campaign.export.list')
        ->middleware('permission:campaign.report');

    Route::get('/campaigns/{campaign}/export-detail', [CampaignController::class, 'exportDetail'])
    ->name('campaign.export-detail');

    Route::resource('campaign', CampaignController::class);

    Route::get('/calendar', [CampaignHistoryController::class, 'calendar'])
        ->name('calendar.show')
        ->middleware('permission:campaign.history.calendar');

    Route::prefix('/history/campaigns')->group(function () {
        Route::get('/', [CampaignHistoryController::class, 'index'])
            ->name('campaignsHistory.history')
            ->middleware('permission:campaign.history.view');
        Route::get('{campaign}', [CampaignHistoryController::class, 'show'])
            ->name('campaignsHistory.show')
            ->middleware('permission:campaign.history.view')
            ->withTrashed();
        Route::post('{campaign}/restore', [CampaignHistoryController::class, 'restore'])
            ->name('campaignsHistory.restore')
            ->middleware('permission:campaign.history.restore')
            ->withTrashed();
        Route::post('{campaign}/clone', [CampaignHistoryController::class, 'clone'])
            ->name('campaignsHistory.clone')
            ->middleware('permission:campaign.history.clone')
            ->withTrashed();
    });
});
