<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ContributorRequestController;

// Route untuk register dan login
Route::post('register', [AuthController::class, 'register']); // Step 1: Kirim OTP
Route::post('verify-otp', [AuthController::class, 'verifyOtp']); // Step 2: Verifikasi OTP
Route::post('resend-otp', [AuthController::class, 'resendOtp']); // Kirim ulang OTP
Route::post('login', [AuthController::class, 'login']);

//route untuk mencari dokumen
Route::get('/documents/search', [DocumentController::class, 'search']);

//route untuk konten unggulan
Route::get('/documents/featured-content', [DocumentController::class, 'featuredContent']);

Route::get('/filters', [FilterController::class, 'index']);


// Route yang butuh autentikasi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Contributor request routes
    Route::post('/contributor-requests', [ContributorRequestController::class, 'store']); // User submit request
    Route::get('/contributor-requests/check-pending', [ContributorRequestController::class, 'checkPending']); // Check if user has pending request

    // Super Admin only routes
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin')->group(function () {
        // Roles management (CRUD except index)
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
        Route::get('/permissions', [RoleController::class, 'permissions']);

        // Contributor requests management (approve/reject)
        Route::get('/contributor-requests', [ContributorRequestController::class, 'index']);
        Route::post('/contributor-requests/{id}/approve', [ContributorRequestController::class, 'approve']);
        Route::post('/contributor-requests/{id}/reject', [ContributorRequestController::class, 'reject']);
    });

    // Super Admin dan Admin bisa lihat list roles (untuk dropdown di user management)
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin,admin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);

        // Users management
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Hanya Super Admin dan Admin yang boleh mengelola semua dokumen
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin,admin')->group(function () {
        Route::post('/documents', [DocumentController::class, 'store']);
        Route::put('/documents/{id}', [DocumentController::class, 'update']);
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    });

    // Reviewer hanya bisa meninjau dokumen
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':reviewer')->group(function () {
        Route::get('/documents/review', [DocumentController::class, 'review']);
    });

    // Contributor hanya bisa upload dokumen
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':contributor')->group(function () {
        Route::post('/documents/upload', [DocumentController::class, 'upload']);
    });

    // Guest hanya bisa lihat
    Route::get('/documents', [DocumentController::class, 'index']);

});