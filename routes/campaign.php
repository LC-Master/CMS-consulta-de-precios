<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignHistoryController;

Route::middleware(['auth', 'verified', 'role:admin|publicidad|supervisor|consultor'])->group(function () {
    Route::get('/campaign/activate/{campaign}', [CampaignController::class, 'activate'])->name('campaign.activate');
    Route::get('/campaign/cancel/{campaign}', [CampaignController::class, 'cancel'])->name('campaign.cancel');

    Route::resource('campaign', CampaignController::class);
    Route::get('/calendar', [CampaignHistoryController::class, 'calendar'])->name('calendar.show');
    Route::prefix('/history/campaigns')->group(function () {
        Route::get('/', [CampaignHistoryController::class, 'index'])->name('campaignsHistory.history');
        Route::get('{campaign}', [CampaignHistoryController::class, 'show'])->name('campaignsHistory.show')->withTrashed();
        Route::post('{campaign}/restore', [CampaignHistoryController::class, 'restore'])->name('campaignsHistory.restore')->withTrashed();
        Route::post('{campaign}/clone', [CampaignHistoryController::class, 'clone'])->name('campaignsHistory.clone')->withTrashed();
    });
});
