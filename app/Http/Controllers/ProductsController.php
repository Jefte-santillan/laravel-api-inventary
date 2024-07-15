<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\StoreRequest;
use App\Logics\Products\StoreLogic;
use App\Http\Controllers\Controller;
use App\Logics\Products\IndexLogic;
use App\Http\Requests\Products\IndexRequest;

class ProductsController extends Controller
{
    public function store(StoreRequest $request, StoreLogic $logic)
    {
        $input = $request->allValid();
        $result = $logic->run($input);
        return response()->create($result);
    }

    public function index(IndexRequest $request, IndexLogic $logic)
    {
        $input = $request->allValid();
        $result = $logic->run($input);
        return response()->create($result);
    }
}
