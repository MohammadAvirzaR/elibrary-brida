<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminDocumentController;
use App\Http\Controllers\Api\ContributorRequestController;

// AUTH
Route::post('register', [AuthController::class, 'register']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::post('login', [AuthController::class, 'login']);

// Public document routes (accessible without authentication)
Route::get('/documents/search', [DocumentController::class, 'search']);
Route::get('/documents/featured-content', [DocumentController::class, 'featuredContent']);

// Public file serving - works for both authenticated and guest users
Route::get('/documents/{id}/file', [DocumentController::class, 'serveFile']);
Route::get('/documents/{documentId}/attachments/{attachmentId}/file', [DocumentController::class, 'serveAttachment']);

// View specific document (public access for approved documents) - place AFTER specific routes
Route::get('/documents/{id}', [DocumentController::class, 'show'])->where('id', '[0-9]+');

Route::get('/filters', [FilterController::class, 'index']);

// Auth Sanctum routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Contributor request routes
    Route::post('/contributor-requests', [ContributorRequestController::class, 'store']);
    Route::get('/contributor-requests/check-pending', [ContributorRequestController::class, 'checkPending']);

    // ==================== SUPER ADMIN ONLY ====================
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin')->group(function () {
        // Role management (CRUD)
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
        Route::get('/permissions', [RoleController::class, 'permissions']);

        // Contributor request approval
        Route::get('/contributor-requests', [ContributorRequestController::class, 'index']);
        Route::post('/contributor-requests/{id}/approve', [ContributorRequestController::class, 'approve']);
        Route::post('/contributor-requests/{id}/reject', [ContributorRequestController::class, 'reject']);
    });

    // Specific document routes (must come before /documents/{id})
    // Preview metadata (all authenticated users)
    Route::post('/documents/preview', [DocumentController::class, 'preview']);

    // List dokumen milik user
    Route::get('/documents/my', [DocumentController::class, 'myDocuments']);

    // Review route (Reviewer + Admin + Super Admin)
    Route::get('/documents/review', [DocumentController::class, 'review'])
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':reviewer,admin,super_admin');

    // Upload document (Contributor + Admin + Super Admin)
    Route::post('/documents/upload', [DocumentController::class, 'upload'])
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':contributor,admin,super_admin');

    // ========== ADMIN & SUPER ADMIN ==========
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin,admin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);

        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::post('/admin/documents/{id}/approve', [AdminDocumentController::class, 'approve']);
        Route::post('/admin/documents/{id}/reject', [AdminDocumentController::class, 'reject']);
    });

    Route::get('/documents', [DocumentController::class, 'index']);

    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':contributor')->group(function () {
        Route::post('/documents', [DocumentController::class, 'store']);
    });

    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':contributor,admin,super_admin,reviewer')->group(function () {
        Route::put('/documents/{id}', [DocumentController::class, 'update']);
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    });
});
