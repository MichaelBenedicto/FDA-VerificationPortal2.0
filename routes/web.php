<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FdaAuthController;
use App\Http\Controllers\FdaHRController;
use App\Http\Controllers\CSLBatchNotificationController;
use App\Http\Controllers\CSLLotReleaseController;

Route::get('/', function () {
    return view('app');
});

// Login - Using /fda prefix
Route::get('/fda/login', function () {
    return view('fda.login');
})->name('login'); // Keeping name as 'login' is critical

Route::post('/fda/login', [FdaAuthController::class, 'login']);
Route::post('/fda/logout', [FdaAuthController::class, 'logout']);

// Protected Routes
Route::middleware(['auth:admin'])->group(function() {
    Route::get('/fda/dashboard', [FdaAuthController::class, 'dashboard']);
    Route::get('/fda/user', [FdaAuthController::class, 'getUser']);

    // HR Management
    Route::get('/fda/hr/list', [FdaHRController::class, 'list']);
    Route::post('/fda/hr/add', [FdaHRController::class, 'add']);
    Route::put('/fda/hr/update/{id}', [FdaHRController::class, 'update']);
    Route::get('/fda/hr/download', [FdaHRController::class, 'download']);
    Route::post('/fda/hr/update/{id}', [FdaHRController::class, 'update']);
    Route::get('/fda/hr/view/{id}', [FdaHRController::class, 'view']);

    // CSL Batch Notification
Route::get('/fda/csl/batch-notifications', [CSLBatchNotificationController::class, 'index']);
Route::post('/fda/csl/batch-notifications/add', [CSLBatchNotificationController::class, 'add']);
Route::post('/fda/csl/batch-notifications/update/{id}', [CSLBatchNotificationController::class, 'update']);
Route::post('/fda/csl/batch-notifications/import', [CSLBatchNotificationController::class, 'import']);
// CSL Lot Release
Route::get('/fda/csl/lot-release', [CSLLotReleaseController::class, 'index']);
Route::post('/fda/csl/lot-release/add', [CSLLotReleaseController::class, 'add']);
Route::post('/fda/csl/lot-release/update/{id}', [CSLLotReleaseController::class, 'update']);
Route::post('/fda/csl/lot-release/import', [CSLLotReleaseController::class, 'import']);
    
    
    Route::get('/ADMIN_FDA_EMPLOYEESview.php', function () {
        return view('fda.viewEmployee');
    });

    Route::get('/maintenance', function () {
        return view('maintenance');
    });
});