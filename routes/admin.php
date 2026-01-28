<?php
use App\Http\Controllers\CenterTokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'password.confirm'])->group(function () {
    Route::resource('user', UserController::class);
    Route::resource('centertokens', CenterTokenController::class)->only(['index', 'store', 'destroy']);
});