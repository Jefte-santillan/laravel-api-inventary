<?php

namespace App\Http\Requests\Operations;

use App\Kernel\Http\Requests\FormRequest;

class ShowRequest extends FormRequest
{
    protected $rules = [
        'id' => 'required',
    ];

    protected $errorCode = [
        'id' => 'products.operation_id',
    ];

    public function validationData()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
        return parent::validationData();
    }
}
