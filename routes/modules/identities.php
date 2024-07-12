<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IdentitiesController;
/*
Route::controller(IdentitiesController::class)->group(function () {

}); */


Route::post('', [IdentitiesController::class, 'store']);
Route::get('', [IdentitiesController::class, 'index']);
