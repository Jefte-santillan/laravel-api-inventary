<?php

namespace App\Logics\Operations;

use App\Kernel\Logics\LogicBusinessTrait;
use App\Models\Operations;


class ShowLogic
{
    use LogicBusinessTrait;

    protected $model;
    protected $errorCodeBase = 'inventory.operations.show';
    protected $autoInjectIdentity = false;
    public function __construct(
        protected Operations $operations,
    ) {
        $this->model = $this->operations;
    }

    public function run($input)
    {
        return $this->getReport($input['id']);
    }

    public function getReport($id)
    {
        $result = $this->runQuery($id);
        if (!$result) {
            return $this->errorCode("$this->errorCodeBase.notFound");
        }
        return $result;
    }

    public function runQuery($id)
    {
        return $this->operations->getOperationWidthProduct($id);
    }
}
