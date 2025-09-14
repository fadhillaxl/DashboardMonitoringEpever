<?php

use App\Http\Controllers\ArduinoController;
use App\Http\Controllers\EpeverController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RelayController;
use App\Http\Controllers\SiteController;

Route::get('/', function () {
    return redirect('/login');
});

// hanya bisa diakses kalau BELUM login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// hanya bisa diakses kalau SUDAH login
Route::middleware('auth')->group(function () {
    // Sensor routes
    Route::get('/dashboard/{mac_address}/sensors', [SensorController::class, 'showSite'])
        ->name('sensors.show');
    Route::get('/dashboard/{mac_address}/sensorsCharts', [SensorController::class, 'showCharts'])
        ->name('sensors.charts');

    // Epever routes
    Route::get('/dashboard/{mac_address}/epever', [EpeverController::class, 'showSite'])
        ->name('epever.show');
    Route::get('/dashboard/{mac_address}/epeverCharts', [EpeverController::class, 'showCharts'])
        ->name('epever.charts');

    // Arduino routes
    Route::get('/dashboard/{mac_address}/arduino', [ArduinoController::class, 'showSite'])
        ->name('arduino.show');
    Route::get('/dashboard/{mac_address}/arduinoCharts', [ArduinoController::class, 'showCharts'])
        ->name('arduino.charts');

    // Relay routes
    Route::get('/dashboard/{mac_address}/relayControl',  [RelayController::class, 'controlPage'])->name('relay.control');;
    Route::post('/dashboard/{mac_address}/relayControl', [RelayController::class, 'updateCommand'])->name('relay.updateCommand');


    // Site CRUD dengan MAC sebagai parameter untuk show/edit/update/delete
    Route::prefix('/dashboard/sites')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('sites.index');
        Route::get('/create', [SiteController::class, 'create'])->name('sites.create');
        Route::post('/', [SiteController::class, 'store'])->name('sites.store');

        // show/edit/update/delete berdasarkan mac_address
        Route::get('/{mac_address}', [SiteController::class, 'show'])->name('sites.show');
        Route::get('/{mac_address}/edit', [SiteController::class, 'edit'])->name('sites.edit');
        Route::put('/{mac_address}', [SiteController::class, 'update'])->name('sites.update');
        Route::delete('/{mac_address}', [SiteController::class, 'destroy'])->name('sites.destroy');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
