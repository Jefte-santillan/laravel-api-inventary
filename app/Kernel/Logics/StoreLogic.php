<?php

namespace App\Kernel\Logics;

use App\Kernel\Logics\LogicBusinessTrait;
use App\Kernel\Exceptions\ContextException;

class StoreLogic
{
    use LogicBusinessTrait;

    protected $model;

    protected $autoInjectIdentity = true;

    protected $fieldIdentity = 'identity_id_created';

    protected $deadlockAttempts = 5;

    protected $runTransactionWithDeadlock = true;

    protected $errorCodeBase = 'kernel.store';

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function run(array $input = [])
    {
        if (!$this->injectIdentityCreated($input)) {
            return $this->errorGetIdentity();
        }
        $result = $this->runWithTransaction($input);
        if (!$result) {
            return $this->emitEventError($input);
        }
        $event = $this->emitEventSuccess($result, $input);
        if (!$event) {
            return false;
        }
        return $this->getReturnData($result, $event);
    }

    public function getReturnData(&$result, &$event)
    {
        return $result;
    }

    public function emitEventError(&$input)
    {
        return false;
    }

    public function emitEventSuccess(&$result, &$input)
    {
        return $result;
    }

    public function runWithTransaction(&$input)
    {
        if ($this->runTransactionWithDeadlock) {
            return $this->runTransactionWithDeadlock($input);
        }
        return $this->runWithoutTransaction($input);
    }

    public function getDateNow($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }

    public function runWithoutTransaction(&$input)
    {
        if (!$this->beforeSave($input)) {
            return false;
        }
        $resultSave = $this->save($input);
        if ($resultSave === false) {
            return false;
        }
        $resultAfterSave = $this->afterSave($resultSave, $input);
        if ($resultAfterSave === false) {
            return false;
        }
        return $resultSave;
    }

    public function runTransactionWithDeadlock(&$input)
    {
        $connection = $this->model->getConnection();
        try {
            return $connection->transaction(function () use ($input) {
                if (!$this->beforeSave($input)) {
                    $this->beforeThrowNewExceptionBeforeSave($input);
                    $this->throwNewExceptionBeforeSave();
                }
                $resultSave = $this->save($input);
                if ($resultSave === false) {
                    $this->beforeThrowNewExceptionSave($input);
                    $this->throwNewExceptionSave();
                }
                $resultAfterSave = $this->afterSave($resultSave, $input);
                if ($resultAfterSave === false) {
                    $this->beforeThrowNewExceptionAfterSave($input);
                    $this->throwNewExceptionAfterSave();
                }
                return $resultSave;
            }, $this->getDeadlockAttempts());
        } catch (ContextException $ex) {

            return $this->processBeforeSaveExceptionError($ex);
        } catch (\Exception $ex) {

            return $this->processExceptionError($ex);
        } catch (\Throwable $ex) {

            $this->beforeProcessThrowableError($input, $ex);
            return $this->processThrowableError($ex);
        }
    }

    public function beforeProcessThrowableError($input, $ex)
    {
        return true;
    }

    public function beforeThrowNewExceptionBeforeSave($input)
    {
        return true;
    }

    public function beforeThrowNewExceptionAfterSave($input)
    {
        return true;
    }

    public function beforeThrowNewExceptionSave($input)
    {
        return true;
    }

    public function throwNewExceptionSave()
    {
        throw new \Exception($this->errorCodeBase . '.save');
    }

    public function throwNewExceptionBeforeSave()
    {
        throw new \Exception($this->errorCodeBase . '.beforeSave');
    }

    public function throwNewExceptionAfterSave()
    {
        throw new \Exception($this->errorCodeBase . '.afterSave');
    }

    public function processBeforeSaveExceptionError(
        ContextException $ex
    ) {
        return $this->errorCode(
            $ex->getMessage(),
            $ex->getContext(),
            $ex
        );
    }

    public function processExceptionError($ex)
    {
        return $this->errorCodeSensitive(
            $ex->getMessage(),
            $this->getResponseError($ex)
        );
    }

    public function processThrowableError($ex)
    {
        return $this->errorCodeSensitive(
            $this->errorCodeBase . '.throwable',
            $this->getResponseError($ex)
        );
    }

    public function beforeSave(&$input)
    {
        return true;
    }

    public function afterSave(&$resultSave, &$input)
    {
        return true;
    }

    public function save(&$input)
    {
        return $this->model->create($input);
    }

    public function getDeadlockAttempts()
    {
        return $this->deadlockAttempts;
    }

    public function injectIdentityCreated(&$input)
    {
        if (!$this->getAutoInjectIdentity()) {
            return true;
        }
        if (isset($input[$this->getFieldIdentity()])) {
            return true;
        }
        $identity = $this->getIdentity();
        if (!$identity) {
            return $this->errorGetIdentity();
        }
        $input[$this->getFieldIdentity()] = $identity->id;
        return true;
    }

    public function errorGetIdentity()
    {
        return false;
    }

    public function getFieldIdentity()
    {
        return $this->fieldIdentity;
    }

    public function getAutoInjectIdentity()
    {
        return $this->autoInjectIdentity;
    }

    public function getResponseError($error)
    {
        return [
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
        ];
    }
}
