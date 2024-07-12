<?php

namespace App\Http\Controllers;

use App\Http\Requests\Identities\StoreRequest;
use App\Http\Requests\Identities\IndexRequest;
use App\Logics\Identities\StoreLogic;
use App\Logics\Identities\IndexLogic;
use App\Http\Controllers\Controller;


class IdentitiesController extends Controller
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
        return response()->paging($result);
    }
}
