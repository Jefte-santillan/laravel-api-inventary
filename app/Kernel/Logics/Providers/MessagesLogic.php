<?php

namespace App\Kernel\Logics\Providers;

use Illuminate\Support\Collection;
use Exception;

class MessagesLogic
{

    protected $collection;

    public function __construct($collection = null) {
        if( is_null($collection)) {
            $this->collection = new Collection([]);
        }
    }

    public function debug($message, array $context = [])
    {
        $data = app('arrays')->interpolate($message, $context);
        logger()->debug($message, $context);
        $this->add([
            'type'=>'debug',
            'message'=>$data,
            'context'=>$context
        ]);
        return true;
    }

    public function info($message, array $context = [])
    {
        $data = app('arrays')->interpolate($message, $context);
        logger()->info($message, $context);
        $this->add([
            'type'=>'info',
            'message'=>$message,
            'context'=>$context
        ]);
    }

    public function errorCode(
        $code,
        array $context = [],
        $exception = null,
    )
    {
        $message = $this->getMessageCode($code, $context);
        logger()->error($code, [
            ...$context,
            ...is_null($exception) ?
                [] : $this->exceptionToArray($exception),
        ]);
        $this->add([
            'message' => $message,
            'context' => $context,
            'code' => $code
        ]);
        return false;
    }

    public function exceptionToArray($exception)
    {
        return [
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'message' => $exception->getMessage(),
        ];
    }

    public function errorCodeSensitive($code, array $context = []): bool
    {
        $message = $this->getMessageCode($code);

        logger()->error($code, $context);

        $this->add([
            'message'=>$message,
            'code'=>$code
        ]);

        return false;
    }

    public function infoCode($code, array $context = [])
    {
        $message = $this->getMessageCode($code, $context);
        logger()->info($code, $context);
        $this->add([
            'type'=>'info',
            'message'=>$message ? $message : $context,
            'code'=>$code
        ]);
        return true;
    }

    public function getMessageCode($code, $data = [])
    {
        static $errors = null;
        if (!$errors) {
            $errors = config('errors');
        }
        $message = isset($errors[$code]) ? $errors[$code] : null;
        if (!empty($data) && !is_null($message)) {
            $message = app('arrays')->interpolate($message, $data);
        }
        return $message;
    }

    public function error($message, array $context = [])
    {
        $data = app('arrays')->interpolate($message, $context);
        logger()->error($message, $context);
        $this->add([
            'message'=>$data,
        ]);
        return false;
    }

    public function add($input)
    {
        $message = app('arrays')->mergeDefault($input, [
            'type'=>'error',
            'message'=>'',
            'code'=>''
        ]);
        $this->addMessage($message);
    }

    public function get()
    {
        if( config('app.env') === 'local') {
            return $this->collection->all();
        }
        return $this->getErrors();
    }

    public function getErrors()
    {
        return $this->collection->where('type', 'error')->map(function($record) {
            return collect($record)->only('code', 'message', 'context')->toArray();
        })->values()->toArray();
    }

    public function getInfo()
    {
        return $this->collection->where('type', 'info')->map(function($record) {
            return collect($record)->only('code', 'message')->toArray();
        })->values();
    }

    public function getDebug()
    {
        return $this->collection->where('type', 'debug')->map(function($record) {
            return collect($record)->only('context', 'message')->toArray();
        })->values();
    }

    public function getAllTypes()
    {
        return [
            'errors'=>$this->getErrors(),
            'info'=>$this->getInfo(),
            'debug'=>$this->getDebug(),
        ];
    }

    private function addMessage($message)
    {
        $this->collection->push($message);
        return $message;
    }

    public function existErrorCode($errorCode)
    {
        $existError = $this->findErrorCode($errorCode);
        return $existError ? true : false;
    }

    public function existErrorCodes(array $errorCodes): bool
    {
        $errors = $this->findErrorCodes($errorCodes);
        if (count($errors) > 0) {
            return true;
        }

        return false;
    }

    public function findErrorCode($errorCode)
    {
        return $this->collection
            ->where('type', 'error')
            ->where('code', $errorCode)
            ->first();
    }

    public function findErrorCodes($errorCodes)
    {
        return $this->collection
            ->where('type', 'error')
            ->whereIn('code', $errorCodes)
            ->values()
            ->toArray();
    }

}
