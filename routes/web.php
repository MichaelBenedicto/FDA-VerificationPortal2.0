<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminHRController;

Route::get('/', function () {
    return view('app');
});
//login
Route::get('/admin/login', function () {
    return view('admin.login');
});

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

Route::middleware(['auth:admin'])->group(function() {
    Route::get('/admin/dashboard', [AdminAuthController::class, 'dashboard']);
});

Route::get('/admin/user', [AdminAuthController::class, 'getUser'])
    ->middleware('auth:admin');
//HR

Route::middleware('auth:admin')->group(function () {
Route::get('/admin/hr/list', [AdminHRController::class, 'list']);
Route::post('/admin/hr/add', [AdminHRController::class, 'add']);
Route::put('/admin/hr/update/{id}', [AdminHRController::class, 'update']);
Route::get('/admin/hr/download', [AdminHRController::class, 'download']);
Route::post('/admin/hr/update/{id}', [AdminHRController::class, 'update']);
Route::get('/admin/hr/view/{id}', [AdminHRController::class, 'view']);

Route::get('/ADMIN_FDA_EMPLOYEESview.php', function () {
    return view('admin.viewEmployee');
});

Route::get('/maintenance', function () {
    return view('maintenance');
});



});

