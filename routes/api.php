<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SliderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Categories
    Route::get('categories', [CategoryController::class, 'index']);
    
    // Products
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/featured', [ProductController::class, 'featured']);
    Route::get('products/upcoming', [ProductController::class, 'upcoming']);
    Route::get('products', [ProductController::class, 'index']);
    
    // Sliders
    Route::get('sliders', [SliderController::class, 'index']);
});
