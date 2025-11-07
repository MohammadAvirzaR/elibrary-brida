<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;

// Route untuk register dan login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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
    Route::middleware(['role:Super Admin'])->group(function () {
        // Roles management
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
        Route::get('/permissions', [RoleController::class, 'permissions']);

        // Users management
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Hanya Super Admin dan Admin yang boleh mengelola semua dokumen
    Route::middleware(['role:Super Admin,Admin'])->group(function () {
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::put('/documents/{id}', [DocumentController::class, 'update']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    });

    // Reviewer hanya bisa meninjau dokumen
    Route::middleware(['role:Reviewer'])->group(function () {
        Route::get('/documents/review', [DocumentController::class, 'review']);
    });

    // Contributor hanya bisa upload dokumen
    Route::middleware(['role:Contributor'])->group(function () {
        Route::post('/documents/upload', [DocumentController::class, 'upload']);
    });

    // Guest hanya bisa lihat
    Route::get('/documents', [DocumentController::class, 'index']);

});
