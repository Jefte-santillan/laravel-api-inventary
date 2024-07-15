<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductsController;

Route::controller(ProductsController::class)->group(function () {
    Route::post('', 'store');
    Route::get('', 'index');
});
