<?php

namespace App\Http\Controllers;

use App\Http\Requests\Operations\StoreRequest;
use App\Http\Requests\Operations\OutRequest;
use App\Http\Requests\Operations\IndexRequest;
use App\Logics\Operations\StoreLogic;
use App\Logics\Operations\OutLogic;
use App\Logics\Operations\IndexLogic;
use App\Logics\Operations\ShowLogic;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\ShowRequest;
use Illuminate\Http\JsonResponse;

class OperationsController extends Controller
{
    public function store(StoreRequest $request, StoreLogic $logic)
    {
        $input = $request->allValid();
        $result = $logic->run($input);
        return response()->create($result);
    }

    public function out(OutRequest $request, OutLogic $logic)
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

    public function show(
        ShowRequest $request,
        ShowLogic $logic
    ): JsonResponse {
        $input = $request->allValid();
        $result = $logic->run($input);
        return response()->create($result);
    }
}
