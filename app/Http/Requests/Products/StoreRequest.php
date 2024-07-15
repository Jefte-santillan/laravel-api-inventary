<?php

namespace App\Http\Requests\Products;

use App\Kernel\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $rules = [
        'name' => 'required',
        'created_identity_id' => 'required',
    ];
    protected $errorCode = [
        'name' => 'products.name',
        'created_identity_id' => 'products.created_identity_id',
    ];
}
