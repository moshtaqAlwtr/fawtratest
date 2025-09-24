<?php

use Illuminate\Support\Facades\Route;
use Modules\Branches\Http\Controllers\BranchesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('branches', BranchesController::class)->names('branches');
});
