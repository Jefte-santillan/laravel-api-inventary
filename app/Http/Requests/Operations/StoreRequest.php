<?php

namespace App\Http\Requests\Operations;

use App\Kernel\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $rules = [
        'product_id' => 'required',
        'number_serie' => 'required',
        'created_identity_id' => 'required',
    ];
    protected $errorCode = [
        'product_id' => 'products.product_id',
        'number_serie' => 'products.number_serie',
        'created_identity_id' => 'products.created_identity_id',
    ];
}
