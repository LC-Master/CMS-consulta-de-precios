<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ThumbnailController;
use App\Http\Controllers\TimeLineController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\CenterTokenController;

Route::get('/', function (Request $req) {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', action: function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    Route::get('/campaign/activate/{campaign}', [CampaignController::class, 'activate'])->name('campaign.activate');
    Route::get('/campaign/finish/{campaign}', [CampaignController::class, 'finish'])->name('campaign.finish');
    Route::resource('media', MediaController::class)->parameters([
        'media' => 'media'
    ]);
    Route::get('/media/cdn/{media}', [MediaController::class, 'preview']);
    Route::resource('campaign', CampaignController::class);
    Route::resource('timeline', TimeLineController::class);
    Route::resource('agreement', AgreementController::class);
    Route::post('/media/upload', [MediaController::class, 'store'])->name('video.upload');
    Route::get('thumbnail/cdn/{thumbnail}', [ThumbnailController::class, 'show']);
});
Route::middleware(['auth', 'verified','password.confirm'])->group(function () {
    Route::resource('user', UserController::class);
    Route::resource('centertokens', CenterTokenController::class);
});
require __DIR__ . '/settings.php';
