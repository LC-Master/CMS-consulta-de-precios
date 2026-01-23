<?php
use App\Http\Controllers\CenterTokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin|supervisor', 'password.confirm'])->group(function () {
    Route::resource('user', UserController::class);
    Route::resource('centertokens', CenterTokenController::class);
});