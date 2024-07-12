<?php

namespace App\Kernel\Logics\Providers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Response;
use App\Kernel\Logics\Logs\StoreLogic;

class ResponseLogic
{

    public function responseCreate($value)
    {
        $data = $this->addDefaultResult($value);
        $this->addBenchMark($data);
        if ($value) {
            $data['data'] = $value;
            $status = 201;
        } else {
            $status = 400;
        }
        $response = $this->addMessages($data);
        return $this->responseJson($value, $response, $status);
    }

    public function responseData($value, $status = 200)
    {
        $data = $this->addDefaultResult($value);
        $this->addBenchMark($data);
        if ($value) {
            $data['data'] = $value;
        } else {
            $status = 400;
        }
        $response = $this->addMessages($data);
        return $this->responseJson($value, $response, $status);
    }

    public function responseDataWithLog($value, $input = [], $key, $description, $status = 200)
    {
        $data = $this->addDefaultResult($value);
        $this->addBenchMark($data);
        if ($value) {
            $data['data'] = $value;
        } else {
            $status = 400;
        }
        $response = $this->addMessages($data);
        $response['input'] ??= $input;
        $responseJson = $this->responseJson($value, $response, $status);
        if ($status != 200) {
            $saveLog = app(StoreLogic::class)->run(
                [
                    'key' => $key,
                    'description' => $description,
                    'payload' => $responseJson
                ]
            );
        }
        return $responseJson;
    }


    public function responsePaging($value)
    {
        $data = $this->addDefaultResult($value);
        $this->addBenchMark($data);
        if ($value) {
            $data += $value;
        }
        $response = $this->addMessages($data);
        return $this->responseJson($value, $response);
    }

    public function responseUnauthenticated($message)
    {
        $data = $this->addDefaultResult($message);
        app('messages')->errorCode('security.auth.unauthenticated');
        $this->addBenchMark($data);
        $response = $this->addMessages($data);
        return Response::json($response, 401);
    }

    public function responseUnauthorized($message)
    {
        $data = $this->addDefaultResult($message);
        $this->addBenchMark($data);
        $response = $this->addMessages($data);
        return Response::json($response, 401);
    }

    public function responseJson(&$value, &$response, $status = 200)
    {
        if ($value) {
            return Response::json($response, $status);
        }
        return Response::json($response, $status);
    }

    public function addDefaultResult(&$value)
    {
        return [
            'success' => $value ? true : false,
        ];
    }

    public function addMessages(&$data)
    {
        $messages = app('messages')->getAllTypes();
        return app('arrays')->mergeDefault($messages, $data);
    }

    public function addBenchMark(&$data)
    {
        if (env('APP_ENV') === 'local') {
            $data['benchmark'] = [
                'memory' => round(memory_get_usage() / 1024 / 1024, 2) . 'MB',
                'time' => microtime(true) - LARAVEL_START
            ];
        }
    }

    public function responseValidationException(Validator $validator)
    {
        $errors = $validator->getMessageBag()->toArray();
        $messages = app('messages');
        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $message) {
                if (isset($validator->errorCode) && isset($validator->errorCode[$field])) {
                    $messages->add([
                        'code' => $validator->errorCode[$field],
                        'context' => ['field' => $field, 'message' => $message],
                        'message' => "Field $field : $message"
                    ]);
                } else {
                    $messages->add([
                        'context' => ['field' => $field, 'message' => $message],
                        'message' => "Field $field : $message"
                    ]);
                }
            }
        }
        $value = false;
        $data = $this->addDefaultResult($value);
        $this->addBenchMark($data);
        $response = $this->addMessages($data);
        return $this->responseJson($value, $response, 400);
    }
}
