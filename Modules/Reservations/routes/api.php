<?php

use Illuminate\Support\Facades\Route;
use Modules\Reservations\Http\Controllers\ReservationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('reservations', ReservationsController::class)->names('reservations');
});
