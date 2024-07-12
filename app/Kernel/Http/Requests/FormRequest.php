<?php

namespace App\Kernel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class FormRequest extends Request
{

    public function authorize()
    {
        return true;
    }

    public function allValid()
    {
        $rules = $this->dropWildCardRules();
        return $this->only(array_keys($rules));
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function withValidator($validator)
    {
        $validator->errorCode = $this->getErrorCode();
    }

    public function rules()
    {
        return $this->rules;
    }

    protected function dropWildCardRules()
    {
        return array_filter($this->rules(), function ($key) {
            return !str_contains($key, '*');
        }, ARRAY_FILTER_USE_KEY);
    }

    public function validateRows(array $input, array $rules, array $errorCode)
    {
        $rows = [];
        foreach ($input as $row) {
            $validator = validator()->make($row, $rules);
            if ($validator->fails()) {
                $validator->errorCode = $errorCode;
                return $this->failedValidation($validator);
            }
            $rows[] = collect($row)->only(array_keys($rules))->toArray();
        }

        return $rows;
    }
}
