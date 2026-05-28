<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Api\DrugEstablishmentController;
use App\Http\Controllers\Api\DrugProductController;
use App\Http\Controllers\Api\DeviceEstablishmentController;
use App\Http\Controllers\Api\DeviceProductController;
use App\Http\Controllers\Api\DeviceNotificationController;
use App\Http\Controllers\Api\FoodEstablishmentController;
use App\Http\Controllers\Api\FoodProductController;

/*
|--------------------------------------------------------------------------
| 1. Public Routes
|--------------------------------------------------------------------------
| Open to everyone (No Auth Required)
*/
Route::get('/search', [SearchController::class, 'search']);


/*
|--------------------------------------------------------------------------
| 2. External API Routes (Token Protected)
|--------------------------------------------------------------------------
| Requires 'X-API-KEY' header. Best for external apps or scripts.
*/
Route::middleware('api.token')->group(function () {
    Route::get('/drugs/establishments', [DrugEstablishmentController::class, 'index']);
    Route::get('/drugs/products', [DrugProductController::class, 'index']);
    Route::get('/device/establishments', [DeviceEstablishmentController::class, 'index']);
    Route::get('/device/products', [DeviceProductController::class, 'index']);
     Route::get('/device/notifications', [DeviceNotificationController::class, 'index']);
    Route::get('/food/establishments', [FoodEstablishmentController::class, 'index']);
    Route::get('/food/products', [FoodProductController::class, 'index']);
});


/*
|--------------------------------------------------------------------------
| 3. Admin Dashboard Routes (Session Protected)
|--------------------------------------------------------------------------
| Requires the user to be logged in via the Admin Login form.
*/
Route::middleware(['auth:admin'])->group(function () {
    // This route is used by your React useEffect to fetch the logged-in admin's data
    Route::get('/user', [App\Http\Controllers\FdaAuthController::class, 'getUser']);
    
    // Add other dashboard-specific internal APIs here
});