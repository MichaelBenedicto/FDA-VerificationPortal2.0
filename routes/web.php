<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FdaAuthController;
use App\Http\Controllers\FdaHRController;
use App\Http\Controllers\CSLBatchNotificationController;
use App\Http\Controllers\CSLLotReleaseController;
use App\Http\Controllers\CDRRHR\CDRRHRcprController;
use App\Http\Controllers\CDRRHR\CDRRHRhcwController;
use App\Http\Controllers\CDRRHR\CDRRHRwpsController;
use App\Http\Controllers\CCHUHSRR\eportalLTOController;
use App\Http\Controllers\CCHUHSRR\HUP_CCHUHSRR_Controller;
use App\Http\Controllers\CDRRHR\CDRRHRmanualCMDNController;


Route::get('/', function () {
    return view('app');
});

// Login - Using /fda prefix
Route::get('/fda/login', function () {
    return view('fda.login');
})->name('login'); // Keeping name as 'login' is critical

Route::post('/fda/login', [FdaAuthController::class, 'login'])
    ->middleware('throttle:login');
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
 // CDRRHR CPR   
Route::get('/fda/cdrrhr/cpr', [CDRRHRcprController::class, 'index']);
Route::post('/fda/cdrrhr/cpr/add', [CDRRHRcprController::class, 'add']);
Route::post('/fda/cdrrhr/cpr/update/{id}', [CDRRHRcprController::class, 'update']);
Route::post('/fda/cdrrhr/cpr/import', [CDRRHRcprController::class, 'import']);
 // CDRRHR HCW
Route::get('/fda/cdrrhr/hcw', [CDRRHRhcwController::class, 'index']);
Route::post('/fda/cdrrhr/hcw/add', [CDRRHRhcwController::class, 'add']);
Route::post('/fda/cdrrhr/hcw/update/{id}', [CDRRHRhcwController::class, 'update']);
Route::post('/fda/cdrrhr/hcw/import', [CDRRHRhcwController::class, 'import']);
//CDRRHR WPS
Route::get('/fda/cdrrhr/wps', [CDRRHRwpsController::class, 'index']);
Route::post('/fda/cdrrhr/wps/add', [CDRRHRwpsController::class, 'add']);
Route::post('/fda/cdrrhr/wps/update/{id}', [CDRRHRwpsController::class, 'update']);
Route::post('/fda/cdrrhr/wps/import', [CDRRHRwpsController::class, 'import']);
//CDRRHR CMDN Manual
Route::get('/fda/cdrrhr/cmdn_manual', [CDRRHRmanualCMDNController::class, 'index']);
Route::post('/fda/cdrrhr/cmdn_manual/add', [CDRRHRmanualCMDNController::class, 'add']);
Route::post('/fda/cdrrhr/cmdn_manual/update/{id}', [CDRRHRmanualCMDNController::class, 'update']);
Route::post('/fda/cdrrhr/cmdn_manual/import', [CDRRHRmanualCMDNController::class, 'import']);
//elto eportal
Route::get('/fda/cchuhsrr/lto_eportal', [eportalLTOController::class, 'index']);
Route::post('/fda/cchuhsrr/lto_eportal/add', [eportalLTOController::class, 'add']);
Route::post('/fda/cchuhsrr/lto_eportal/update/{id}', [eportalLTOController::class, 'update']);
Route::post('/fda/cchuhsrr/lto_eportal/import', [eportalLTOController::class, 'import']);
//CCHUHSRR HUP CPR
Route::get('/fda/cchuhsrr/hup_cpr', [HUP_CCHUHSRR_Controller::class, 'index']);
Route::post('/fda/cchuhsrr/hup_cpr/add', [HUP_CCHUHSRR_Controller::class, 'add']);
Route::post('/fda/cchuhsrr/hup_cpr/update/{id}', [HUP_CCHUHSRR_Controller::class, 'update']);
Route::post('/fda/cchuhsrr/hup_cpr/import', [HUP_CCHUHSRR_Controller::class, 'import']);

Route::get('/ADMIN_FDA_EMPLOYEESview.php', function () {
        return view('fda.viewEmployee');
    });

    Route::get('/maintenance', function () {
        return view('maintenance');
    });
});