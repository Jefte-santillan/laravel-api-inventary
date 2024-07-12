<?php

namespace App\Http\Requests\Identities;

use App\Kernel\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $rules = [
        'name' => 'required',
    ];
    protected $errorCode = ['name' => 'error'];
}
