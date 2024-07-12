<?php

namespace App\Kernel\Logics\Logs;

use App\Kernel\Logics\LogicBusinessTrait;
use Exception;

class StoreLogic
{
    use LogicBusinessTrait;
    protected $logs;
    protected $events;
    protected $logStatus;
    protected $baseErrorCode;

    public function __construct()
    {
        $this->logs = app(config('appKernel.instances.models.logs'));
        $this->events = app(config('appKernel.instances.models.events'));
        $this->logStatus = app(config('appKernel.instances.models.logStatus'));
    }

    public function run($input)
    {
        $this->baseErrorCode = $input['key'];
        return $this->insertLog($input['key'], $input['description'], json_encode($input['payload']));
    }

    public function getLogEvent($key, $description)
    {
        $result = $this->events->firstOrCreate(['key' => $key], ['description' => $description]);
        if (!$result) {
            return $this->errorCode($this->baseErrorCode . '.findEventByKeyError', [
                'key' => $key
            ]);
        }
        return $result;
    }

    public function getLogStatus($name)
    {
        $result = $this->logStatus->firstWhere('name', $name);
        if (!$result) {
            return $this->errorCode($this->baseErrorCode . '.getLogStatusError', [
                'name' => $name,
            ]);
        }
        return $result;
    }

    public function insertLog($eventKey, $eventDescription, $payload)
    {
        $logEvent = $this->getLogEvent($eventKey, $eventDescription);
        if (!$logEvent) {
            return false;
        }

        $logStatus = $this->getLogStatus('ABIERTO');
        if (!$logStatus) {
            return false;
        }

        $result = $this->logs->saveLog($logStatus['id'], $logEvent['id'], $this->getIdentity()->id, $payload);
        if (!$result) {
            return $this->errorCode($this->baseErrorCode . '.saveLogError', [
                'event' => $event,
                'payload' => $payload
            ]);
        }
        return $result;
    }
}
