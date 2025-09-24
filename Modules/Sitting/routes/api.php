<?php

use Illuminate\Support\Facades\Route;
use Modules\Sitting\Http\Controllers\SittingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sittings', SittingController::class)->names('sitting');
});
