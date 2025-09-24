<?php

use Illuminate\Support\Facades\Route;
use Modules\PointsAndBlances\Http\Controllers\PointsAndBlancesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pointsandblances', PointsAndBlancesController::class)->names('pointsandblances');
});
