<?php

use Illuminate\Support\Facades\Route;
use Modules\Memberships\Http\Controllers\MembershipsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('memberships', MembershipsController::class)->names('memberships');
});
