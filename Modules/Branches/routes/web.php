<?php

use Illuminate\Support\Facades\Route;
use Modules\Branches\Http\Controllers\BranchesController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('branches')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/index', [BranchesController::class, 'index'])->name('branches.index'); // عرض جميع الفروع
            Route::get('/create', [BranchesController::class, 'create'])->name('branches.create'); // نموذج إضافة فرع جديد
            Route::post('/store', [BranchesController::class, 'store'])->name('branches.store'); // تخزين فرع جديد
            Route::get('/show/{id}', [BranchesController::class, 'show'])->name('branches.show'); // عرض تفاصيل فرع معين
            Route::get('/edit/{id}', [BranchesController::class, 'edit'])->name('branches.edit'); // عرض نموذج تعديل فرع
            Route::put('/update/{id}', [BranchesController::class, 'update'])->name('branches.update'); // تحديث بيانات الفرع
            Route::get('/updateStatus/{id}', [BranchesController::class, 'updateStatus'])->name('branches.updateStatus'); // تحديث بيانات الفرع
            Route::delete('/delete/{id}', [BranchesController::class, 'destroy'])->name('branches.destroy'); // حذف فرع
            Route::get('/settings', [BranchesController::class, 'settings'])->name('branches.settings');
            Route::post('/settings', [BranchesController::class, 'settings_store'])->name('branches.settings_store');
            Route::get('/branches/{branch}/settings', [BranchesController::class, 'getBranchPermissions'])->name('branches.loadSettings');
            Route::get('/switch-branch/{branch}', [BranchesController::class, 'switchBranch'])->name('branch.switch');
            Route::get('/settings/get-settings', [BranchesController::class, 'getSettings'])->name('settings.get');
            Route::get('/ajax', [BranchesController::class, 'getBranches'])->name('branches.ajax');
        });
});
