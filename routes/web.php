<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TimeLineController;
use App\Http\Controllers\AgreementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function (Request $req) {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('campaign', CampaignController::class);
    Route::resource('timeline', TimeLineController::class);
    Route::resource('agreement', AgreementController::class);
});

Route::get('/video', function () {
    return Inertia::render('video');
});
Route::post('/media/upload', [MediaController::class, 'store'])->name('video.upload');

Route::get('/files', fn () => response()->file(Storage::disk('public')->path('uploads/ORC4PXDMTUIwA83x2fotA1Iakml6Fw5EmyC3lgwm.mp4')));

require __DIR__.'/settings.php';
