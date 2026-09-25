<?php

use App\Http\Controllers\Api\BasketController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('products', [ProductController::class, 'index']);

Route::controller(BasketController::class)->prefix('basket')->group(function () {
    Route::get('/', 'show');
    Route::delete('/', 'destroy');
    Route::post('items', 'store');
    Route::delete('items/{code}', 'destroyItem');
});
