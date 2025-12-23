<?php

use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::get('/generateApiToken', function () {
    $center = Center::select('id', 'name', 'code')->first();
    $token = $center->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token]);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
