<?php

namespace App\Logics\Operations;

use App\Kernel\Logics\StoreLogic as Logics;
use App\Models\Operations;
use App\Models\Identities;
use App\Models\Products;
use App\Models\TypeOperations;

class StoreLogic extends Logics
{
    protected $errorCodeBase = 'inventory.operations.store';
    protected $autoInjectIdentity = false;
    public function __construct(
        protected Operations $operations,
        protected Identities $identities,
        protected Products $products,
        protected TypeOperations $typeOperations
    ) {
        $this->model = $this->operations;
    }

    public function beforeSave(&$input)
    {
        if (!$this->getIdentityById($input)) {
            return false;
        }
        if (!$this->getProductById($input)) {
            return false;
        }
        $operation = $this->getOperationInByKey();
        if (!$operation) {
            return false;
        }
        $input['operation_type_id'] = $operation->id;
        return true;
    }

    public function afterSave(&$input, &$changedAttributes)
    {
        $product = $this->products->find($input['product_id']);
        $product->quantity = $product->quantity + 1;
        $product->save();
        return true;
    }


    public function getIdentityById($input)
    {
        $identity = $this->identities->find($input['created_identity_id']);
        if (!$identity) {
            return $this->errorCode("$this->errorCodeBase.identityNotFound");
        }
        return true;
    }

    public function getProductById($input)
    {
        $identity = $this->products->find($input['product_id']);
        if (!$identity) {
            return $this->errorCode("$this->errorCodeBase.productNotFound");
        }
        return true;
    }

    public function getOperationById($input)
    {
        $operation = $this->operations->find($input['operation_type_id']);
        if (!$operation) {
            return $this->errorCode("$this->errorCodeBase.operationTypeNotFound");
        }
        return true;
    }

    public function getOperationInByKey()
    {
        $operation = $this->typeOperations->getByKey(TypeOperations::IN_KEY);
        if (!$operation) {
            return $this->errorCode("$this->errorCodeBase.typeOperationNotFound");
        }
        return $operation;
    }
}
