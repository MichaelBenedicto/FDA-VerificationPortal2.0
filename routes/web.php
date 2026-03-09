<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminHRController;

Route::get('/', function () {
    return view('app');
});

// Login - Using /fda prefix
Route::get('/fda/login', function () {
    return view('admin.login');
})->name('login'); // Keeping name as 'login' is critical

Route::post('/fda/login', [AdminAuthController::class, 'login']);
Route::post('/fda/logout', [AdminAuthController::class, 'logout']);

// Protected Routes
Route::middleware(['auth:admin'])->group(function() {
    Route::get('/fda/dashboard', [AdminAuthController::class, 'dashboard']);
    Route::get('/fda/user', [AdminAuthController::class, 'getUser']);

    // HR Management
    Route::get('/fda/hr/list', [AdminHRController::class, 'list']);
    Route::post('/fda/hr/add', [AdminHRController::class, 'add']);
    Route::put('/fda/hr/update/{id}', [AdminHRController::class, 'update']);
    Route::get('/fda/hr/download', [AdminHRController::class, 'download']);
    Route::post('/fda/hr/update/{id}', [AdminHRController::class, 'update']);
    Route::get('/admin/hr/view/{id}', [AdminHRController::class, 'view']);

    Route::get('/ADMIN_FDA_EMPLOYEESview.php', function () {
        return view('admin.viewEmployee');
    });

    Route::get('/maintenance', function () {
        return view('maintenance');
    });
});