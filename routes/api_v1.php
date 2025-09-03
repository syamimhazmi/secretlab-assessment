<?php

use App\Http\Controllers\KeyValueStoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/object', [KeyValueStoreController::class, 'store'])
    ->middleware('throttle:100,1');

Route::get('/object/{key}', [KeyValueStoreController::class, 'show']);
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
