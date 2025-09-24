<?php

use Illuminate\Support\Facades\Route;
use Modules\RentalManagement\Http\Controllers\RentalManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rentalmanagements', RentalManagementController::class)->names('rentalmanagement');
});
