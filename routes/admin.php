<?php
use App\Http\Controllers\CenterTokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'password.confirm'])->group(function () {
    Route::resource('user', UserController::class);
  
    Route::put('/users/{user}/restore', [UserController::class, 'restore'])
        ->withTrashed()
        ->middleware('permission:users.restore')
        ->name('user.restore'); 
  
    Route::resource('centertokens', CenterTokenController::class)
        ->only(['index', 'store', 'destroy']);
});