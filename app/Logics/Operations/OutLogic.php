<?php

namespace App\Logics\Operations;

use App\Kernel\Logics\StoreLogic as Logics;
use App\Models\Operations;
use App\Models\Identities;
use App\Models\Products;
use App\Models\TypeOperations;
use App\Kernel\Logics\LogicBusinessTrait;

class OutLogic
{
    use LogicBusinessTrait;
    protected $errorCodeBase = 'inventory.operations.out';
    protected $autoInjectIdentity = false;
    public function __construct(
        protected Operations $operations,
        protected Identities $identities,
        protected Products $products,
        protected TypeOperations $typeOperations
    ) {
    }

    public function run($input)
    {
        try {
            $operation = $this->operations->getOperation($input['operation_id']);
            if (!$operation) {
                return $this->errorCode("$this->errorCodeBase.operationNotFound");
            }
            $operation->out = time();
            $operation->out_identity_id = $input['identity_id'];
            $operation->save();
            $product = $this->products->find($operation->product_id);
            $product->quantity = $product->quantity - 1;
            $product->save();
            return $operation;
        } catch (\Throwable $th) {
            return $this->errorCode("$this->errorCodeBase.erroSave", [
                'th' => $th->getMessage()
            ], $th);
        }
    }
}
