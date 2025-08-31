<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\ChangePasswordController;
use App\Http\Controllers\Api\Auth\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('register', RegisterController::class);
        Route::post('login', LoginController::class);
        Route::post('forgot-password', ForgotPasswordController::class);
        Route::post('reset-password', [ForgotPasswordController::class, 'reset']);
    });

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('profile', [ProfileController::class, 'show']);
            Route::put('profile', [ProfileController::class, 'update']);
            Route::post('change-password', ChangePasswordController::class);
            Route::post('logout', [ProfileController::class, 'logout']);
        });
    });

    // Public routes (no authentication required)
    Route::get('categories', [CategoryController::class, 'index']);

    Route::prefix('products')->group(function () {
        Route::get('search', [ProductController::class, 'search']);
        Route::get('featured', [ProductController::class, 'featured']);
        Route::get('upcoming', [ProductController::class, 'upcoming']);
        Route::get('/', [ProductController::class, 'index']);
    });

    Route::get('sliders', [SliderController::class, 'index']);
});
