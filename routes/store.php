<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/stores', [StoreController::class, 'index'])
        ->name('stores.index');
    Route::post('/stores/{store}/force-sync', [StoreController::class, 'forceSync'])
        ->name('stores.force.sync');
    Route::post('/stores/{store}/force-playlist', [StoreController::class, 'forcePlaylist'])
        ->name('stores.force.playlist');
    Route::post('/stores/{store}/update-sync-url', [StoreController::class, 'updateSyncUrl'])
        ->name('stores.sync.url.update');
    Route::post('/stores/{store}/update-placeholder', [StoreController::class, 'updatePlaceholder'])
        ->name('stores.placeholder.update');
});