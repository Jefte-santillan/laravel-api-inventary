<?php

namespace App\Http\Requests\Operations;

use App\Kernel\Http\Requests\FormRequest;

class IndexRequest extends FormRequest
{
    protected $rules = [
        'page' => 'sometimes',
        'query' => 'sometimes',
        'limit' => 'sometimes',
    ];
    protected $errorCode = [
        'page' => 'error',
        'query' => 'error',
        'limit' => 'error',
    ];
}
