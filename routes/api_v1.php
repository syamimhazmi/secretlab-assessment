<?php

use App\Http\Controllers\KeyValueStoreController;
use Illuminate\Support\Facades\Route;

Route::post('/object', [KeyValueStoreController::class, 'store'])
    ->middleware('throttle:100,1');

Route::get('/object/get_all_records', [KeyValueStoreController::class, 'getAllRecords']);

Route::get('/object/{key}', [KeyValueStoreController::class, 'show']);
