<?php

use Illuminate\Support\Facades\Route;
use Modules\InsuranceAgents\Http\Controllers\InsuranceAgentsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('insuranceagents', InsuranceAgentsController::class)->names('insuranceagents');
});
