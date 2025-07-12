<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VisitController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Route untuk kebutuhan aplikasi mobile Sales Canvasser
*/

// === Auth ===
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']); // ✅ Alias untuk user profile
    });
});

// === Protected Routes ===
Route::middleware('auth:sanctum')->group(function () {
    // Visits
    Route::prefix('visits')->group(function () {
        Route::get('/', [VisitController::class, 'index']);
        Route::post('/checkin', [VisitController::class, 'checkin']);
        Route::post('/{visit}/checkout', [VisitController::class, 'checkout']);
    });

    // Customers
    Route::middleware('auth:sanctum')->prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);     // List (with pagination + search)
        Route::get('{id}', [CustomerController::class, 'show']);   // Detail
        Route::post('/', [CustomerController::class, 'store']);    // Create
        Route::put('{id}', [CustomerController::class, 'update']); // Update
        Route::delete('{id}', [CustomerController::class, 'destroy']); // Soft Delete
    });

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        // Route::get('{id}', [ProductController::class, 'show']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
    });
});
