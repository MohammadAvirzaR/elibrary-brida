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
use App\Http\Controllers\Api\DocumentDownloadRequestController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\DebugController;
use App\Http\Controllers\Api\UniversityController;

// AUTH - dengan rate limiting untuk keamanan (update)
Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:3,1'); // Max 3/menit
Route::post('verify-otp', [AuthController::class, 'verifyOtp'])
    ->middleware('throttle:5,1'); // Max 5/menit
Route::post('resend-otp', [AuthController::class, 'resendOtp'])
    ->middleware('throttle:2,1'); // Max 2/menit
Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // Max 5/menit
Route::post('register/google-complete', [AuthController::class, 'completeGoogleRegistration'])
    ->middleware('throttle:5,1'); // Max 5/menit

// Public document routes (accessible without authentication)
Route::get('/documents/search', [DocumentController::class, 'search']);
Route::get('/documents/featured-content', [DocumentController::class, 'featuredContent']);

// View specific document (public access for approved documents) - place AFTER specific routes
Route::get('/documents/{id}', [DocumentController::class, 'show'])->where('id', '[0-9]+');

Route::get('/filters', [FilterController::class, 'index']);

// Download requests — public (no auth needed so guests can request)
Route::post('/download-requests', [DocumentDownloadRequestController::class, 'store']);

// Get all universities (public, untuk dropdown di form upload)
Route::get('/universities', [UniversityController::class, 'index']);

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
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
        Route::put('/roles/{id}/permissions', [RoleController::class, 'syncPermissions']);
        Route::get('/permissions', [RoleController::class, 'permissions']);

        // Contributor request approval
        Route::get('/contributor-requests', [ContributorRequestController::class, 'index']);
        Route::post('/contributor-requests/{id}/approve', [ContributorRequestController::class, 'approve']);
        Route::post('/contributor-requests/{id}/reject', [ContributorRequestController::class, 'reject']);
    });

    // Specific document routes (must come before /documents/{id})
    // Preview metadata (all authenticated users)
    Route::post('/documents/preview', [DocumentController::class, 'preview']);
    Route::get('/documents/{id}/preview', [DocumentController::class, 'servePreview'])
        ->middleware('throttle:30,1');
    Route::get('/documents/{id}/file', [DocumentController::class, 'serveFile'])
        ->middleware('throttle:20,1');
    Route::get('/documents/{documentId}/attachments/{attachmentId}/file', [DocumentController::class, 'serveAttachment'])
        ->middleware('throttle:20,1');

    // List dokumen milik user
    Route::get('/documents/my', [DocumentController::class, 'myDocuments']);

    // Review route (Reviewer + Super Admin)
    Route::get('/documents/review', [DocumentController::class, 'review'])
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':reviewer,super_admin');

    // Review history endpoint (Super Admin)
    Route::get('/documents/review-history', [DocumentController::class, 'getReviewHistory'])
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin');

    // Upload document (Contributor + Super Admin)
    Route::post('/documents/upload', [DocumentController::class, 'upload'])
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':contributor,super_admin');

    // ========== SUPER ADMIN ==========
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':super_admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::post('/admin/documents/{id}/approve', [AdminDocumentController::class, 'approve']);
        Route::post('/admin/documents/{id}/reject', [AdminDocumentController::class, 'reject']);

        // Download request management
        Route::get('/download-requests', [DocumentDownloadRequestController::class, 'index']);
        Route::post('/download-requests/{id}/send', [DocumentDownloadRequestController::class, 'send']);
        Route::post('/download-requests/{id}/reject', [DocumentDownloadRequestController::class, 'reject']);

        // Statistics endpoint (Dashboard)
        Route::get('/statistics', [StatisticsController::class, 'index']);
    });

    Route::get('/documents', [DocumentController::class, 'index']);

    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':contributor')->group(function () {
        Route::post('/documents', [DocumentController::class, 'store']);
    });

    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':contributor,super_admin,reviewer')->group(function () {
        Route::put('/documents/{id}', [DocumentController::class, 'update']);
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    });
});
