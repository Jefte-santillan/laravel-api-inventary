<?php

namespace App\Kernel\Logics;

use App\Kernel\Exceptions\ContextException;

trait LogicBusinessTrait
{

    public function getIdentity()
    {
        return session('security.identity');
    }

    public function bearerToken()
    {
        return session('security.token');
    }

    public function getUser()
    {
        return session('security.user');
    }

    public function captureException($exception)
    {
        app('exception')->captureException($exception);
    }

    public function fireEvent($userId, $eventKey, array $data = [])
    {
        app('binnacle')->create($userId, $eventKey, $data);
    }

    public function track($eventKey, array $data = [])
    {
        app('tracking')->track($eventKey, $data);
    }

    public function errorCode($code, array $data = [], $exception = null,)
    {
        return app('messages')->errorCode($code, $data, $exception);
    }

    public function errorCodeSensitive($code, array $data = [])
    {
        return app('messages')->errorCodeSensitive($code, $data);
    }

    public function error($message, array $data = [])
    {
        return app('messages')->error($message, $data);
    }

    public function debug($message, array $data = [])
    {
        return app('messages')->debug($message, $data);
    }

    public function info($message, array $data = [])
    {
        return app('messages')->info($message, $data);
    }

    public function infoCode($code, array $data = [])
    {
        return app('messages')->infoCode($code, $data);
    }

    public function isGod()
    {
        return app('sdkSecurity')->isGod();
    }

    public function saveLog(string $eventKey, array $payload = [])
    {
        return app('sdkEvents')->saveLog($eventKey, $payload);
    }

    public function launchException(string $message, array $context)
    {
        $contentException = new ContextException($message);
        $contentException->setContext($context);

        throw $contentException;
    }
}
