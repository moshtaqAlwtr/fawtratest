<?php

use Illuminate\Support\Facades\Route;
use Modules\UserGuide\Http\Controllers\UserGuideController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('userguides', UserGuideController::class)->names('userguide');
});
