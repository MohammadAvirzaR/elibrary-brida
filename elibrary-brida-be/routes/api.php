<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminDocumentController;

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

    // Super Admin only routes
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin')->group(function () {
        // Roles management (CRUD except index)
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
        Route::get('/permissions', [RoleController::class, 'permissions']);
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

    Route::post('/documents/preview', [DocumentController::class, 'preview']);

    Route::get('/documents/my', [DocumentController::class, 'myDocuments']);

    // Guest hanya bisa lihat
    Route::get('/documents', [DocumentController::class, 'index']);

    // Public (preview validation)
    Route::post('/documents/preview', [DocumentController::class, 'preview']);

    // Protected: submit final (hanya contributor)
    Route::middleware(['auth:sanctum', \App\Http\Middleware\RoleMiddleware::class . ':contributor'])->group(function () {
        Route::post('/documents', [DocumentController::class, 'store']); // final submit
    });

    // Admin routes: approve/reject (hanya admin & super_admin)
    Route::middleware(['auth:sanctum', \App\Http\Middleware\RoleMiddleware::class . ':super_admin,admin'])->group(function () {
        Route::post('/admin/documents/{id}/approve', [AdminDocumentController::class, 'approve']);
        Route::post('/admin/documents/{id}/reject', [AdminDocumentController::class, 'reject']);
    });

});