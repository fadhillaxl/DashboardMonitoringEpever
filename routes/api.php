<?php

use App\Http\Controllers\API\RelayController;
use App\Http\Controllers\API\SensorsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.key', 'throttle:60,1'])->group(function () {
    Route::post('/sensors', [SensorsController::class, 'store']);
    Route::post('/relay/update', [RelayController::class, 'updateStatus']);
    Route::get('/relay/command', [RelayController::class, 'getCommand']);
});
