<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignHistoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/campaign/activate/{campaign}', [CampaignController::class, 'activate'])
        ->name('campaign.activate')
        ->middleware('permission:campaigns.activate');

    Route::get('/campaign/cancel/{campaign}', [CampaignController::class, 'cancel'])
        ->name('campaign.cancel')
        ->middleware('permission:campaigns.cancel');

    Route::get('/campaigns/report', [CampaignController::class, 'report'])
        ->name('campaign.report.list')
        ->middleware('permission:campaigns.report');
    Route::get('/campaigns/export', [CampaignController::class, 'export'])
        ->name('campaign.export.list')
        ->middleware('permission:campaigns.report');

    Route::resource('campaign', CampaignController::class);

    Route::get('/calendar', [CampaignHistoryController::class, 'calendar'])
        ->name('calendar.show')
        ->middleware('permission:campaigns.history.calendar');

    Route::prefix('/history/campaigns')->group(function () {
        Route::get('/', [CampaignHistoryController::class, 'index'])
            ->name('campaignsHistory.history')
            ->middleware('permission:campaigns.history.view');
        Route::get('{campaign}', [CampaignHistoryController::class, 'show'])
            ->name('campaignsHistory.show')
            ->middleware('permission:campaigns.history.view')
            ->withTrashed();
        Route::post('{campaign}/restore', [CampaignHistoryController::class, 'restore'])
            ->name('campaignsHistory.restore')
            ->middleware('permission:campaigns.history.restore')
            ->withTrashed();
        Route::post('{campaign}/clone', [CampaignHistoryController::class, 'clone'])
            ->name('campaignsHistory.clone')
            ->middleware('permission:campaigns.history.clone')
            ->withTrashed();
    });
});
