<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FilterController;

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

    // Hanya Super Admin dan Admin yang boleh mengelola semua dokumen
    Route::middleware(['auth:sanctum', 'role:Super Admin,Admin'])->group(function () {
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::put('/documents/{id}', [DocumentController::class, 'update']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    });

    // Reviewer hanya bisa meninjau dokumen
    Route::middleware(['auth:sanctum', 'role:Reviewer'])->group(function () {
        Route::get('/documents/review', [DocumentController::class, 'review']);
    });

    // Contributor hanya bisa upload dokumen
    Route::middleware(['auth:sanctum', 'role:Contributor'])->group(function () {
        Route::post('/documents/upload', [DocumentController::class, 'upload']);
    });

    // Guest hanya bisa lihat
    Route::get('/documents', [DocumentController::class, 'index']);

});
