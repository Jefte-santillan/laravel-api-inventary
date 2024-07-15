<?php

use Illuminate\Support\Facades\Route;


Route::prefix('identities')
    ->group(base_path('routes/modules/identities.php'));

Route::prefix('products')
    ->group(base_path('routes/modules/products.php'));

Route::prefix('operations')
    ->group(base_path('routes/modules/operations.php'));
