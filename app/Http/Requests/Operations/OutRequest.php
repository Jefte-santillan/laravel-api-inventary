<?php

namespace App\Http\Requests\Operations;

use App\Kernel\Http\Requests\FormRequest;

class OutRequest extends FormRequest
{
    protected $rules = [
        'operation_id' => 'required',
        'identity_id' => 'required'
    ];

    protected $errorCode = [
        'operation_id' => 'products.operation_id',
        'identity_id' => 'products.identity_id',
    ];

    public function validationData()
    {
        $this->merge([
            'operation_id' => $this->route('operationId'),
        ]);
        return parent::validationData();
    }
}
