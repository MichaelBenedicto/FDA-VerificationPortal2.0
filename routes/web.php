<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FdaAuthController;
use App\Http\Controllers\FdaHRController;

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

    Route::get('/ADMIN_FDA_EMPLOYEESview.php', function () {
        return view('fda.viewEmployee');
    });

    Route::get('/maintenance', function () {
        return view('maintenance');
    });
});