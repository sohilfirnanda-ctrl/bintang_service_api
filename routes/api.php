<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bintang Service API  —  21 endpoint
|--------------------------------------------------------------------------
| Semua URL diawali /api, contoh: http://127.0.0.1:8000/api/services
*/

// ---------- Publik (tanpa token) ----------
Route::post('/register', [AuthController::class, 'register']);              // 1
Route::post('/login', [AuthController::class, 'login']);                    // 2
Route::get('/services', [ServiceController::class, 'index']);               // 3
Route::get('/services/{service}', [ServiceController::class, 'show']);      // 4

// ---------- Wajib token (Bearer) ----------
Route::middleware('auth:sanctum')->group(function () {
    // Akun
    Route::get('/me', [AuthController::class, 'me']);                       // 5
    Route::put('/profile', [AuthController::class, 'updateProfile']);       // 6
    Route::post('/logout', [AuthController::class, 'logout']);              // 7

    // Booking pelanggan
    Route::get('/bookings', [BookingController::class, 'index']);                       // 8
    Route::post('/bookings', [BookingController::class, 'store']);                      // 9
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);              // 10
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);            // 11
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);   // 12
    Route::get('/bookings/{booking}/details', [BookingController::class, 'details']);   // 13

    // ---------- Khusus admin ----------
    Route::middleware('admin')->group(function () {
        Route::post('/services', [ServiceController::class, 'store']);                          // 14
        Route::put('/services/{service}', [ServiceController::class, 'update']);                // 15
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);            // 16
        Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']); // 17

        Route::prefix('admin')->group(function () {
            Route::get('/customers', [AdminController::class, 'customers']);                          // 18
            Route::get('/customers/{customer}', [AdminController::class, 'showCustomer']);            // 19
            Route::get('/customers/{customer}/bookings', [AdminController::class, 'customerBookings']); // 20
            Route::get('/summary', [AdminController::class, 'summary']);                              // 21
        });
    });
});