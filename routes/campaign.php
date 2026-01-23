<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignHistoryController;

Route::middleware(['auth', 'verified', 'role:admin|publicidad'])->group(function () {
    Route::get('/campaign/activate/{campaign}', [CampaignController::class, 'activate'])->name('campaign.activate');
    Route::get('/campaign/finish/{campaign}', [CampaignController::class, 'finish'])->name('campaign.finish');

    Route::get('/campaigns/report', [CampaignController::class, 'report'])->name('campaign.report');
    Route::get('/campaigns/export', [CampaignController::class, 'export'])->name('campaign.export');

    Route::resource('campaign', CampaignController::class);
    Route::get('/calendar', [CampaignHistoryController::class, 'calendar'])->name('calendar.show');
    Route::prefix('/history/campaigns')->group(function () {
        Route::get('/', [CampaignHistoryController::class, 'index'])->name('campaignsHistory.history');
        Route::get('{campaign}', [CampaignHistoryController::class, 'show'])->name('campaignsHistory.show')->withTrashed();
        Route::post('{campaign}/restore', [CampaignHistoryController::class, 'restore'])->name('campaignsHistory.restore')->withTrashed();
        Route::post('{campaign}/clone', [CampaignHistoryController::class, 'clone'])->name('campaignsHistory.clone')->withTrashed();

    });
});