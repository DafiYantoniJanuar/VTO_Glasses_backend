<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TryOnHistoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);

// Public Product Catalog Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Public Reviews List
Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);

// Public Store History (nullable user_id inside controller)
Route::post('/history', [TryOnHistoryController::class, 'store']);
Route::post('/try-on-history', [TryOnHistoryController::class, 'store']);

// Place checkout orders
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);

// Admin / Authenticated Product Management Routes
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

// Favorites, Protected History, & Verified Reviews Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::delete('/favorites/{productId}', [FavoriteController::class, 'destroy']);

    Route::get('/history', [TryOnHistoryController::class, 'index']);
    Route::get('/try-on-history', [TryOnHistoryController::class, 'index']);

    // Review Actions
    Route::get('/products/{id}/review-eligibility', [ReviewController::class, 'checkEligibility']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});


