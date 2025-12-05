<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\TimeLineController;
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
    Route::resource('timeline',TimeLineController::class);
    
});

Route::get('/video', function () {
    return Inertia::render('video');
});

Route::get('/files', function () {
    return response()->file(Storage::disk('public')->path('1416529-hd_1920_1080_30fps.mp4'));
});

require __DIR__.'/settings.php';
