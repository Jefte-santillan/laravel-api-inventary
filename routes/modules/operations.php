<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OperationsController;

Route::controller(OperationsController::class)->group(function () {
    Route::post('', 'store');
    Route::post('/{operationId}/out', 'out');
    Route::get('', 'index');
    Route::get('/{id}', 'show');
});
