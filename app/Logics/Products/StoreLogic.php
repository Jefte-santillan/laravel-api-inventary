<?php

namespace App\Logics\Products;

use App\Kernel\Logics\StoreLogic as Logics;
use App\Models\Products;
use App\Models\Identities;
use App\Models\ProductStatus;

class StoreLogic extends Logics
{
    protected $errorCodeBase = 'inventory.Products.store';
    protected $autoInjectIdentity = false;
    public function __construct(
        protected Products $products,
        protected Identities $identities,
        protected ProductStatus $productStatus
    ) {
        $this->model = $this->products;
    }

    public function beforeSave(&$input)
    {
        if (!$this->getProductByName($input)) {
            return false;
        }
        if (!$this->getIdentityById($input)) {
            return false;
        }
        $status = $this->getProductStatusByKey();
        if (!$status) {
            return false;
        }
        $input['product_status_id'] = $status->id;
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

    public function getProductByName($input)
    {
        $ProductName = $this->products->getByName($input['name']);
        if (!$ProductName) {
            return true;
        }
        return $this->errorCode("$this->errorCodeBase.productAlreadyExist");
    }

    public function getProductStatusByKey()
    {
        $status = $this->productStatus->getByKey(ProductStatus::STATUS_ACTIVE);
        if (!$status) {
            return $this->errorCode("$this->errorCodeBase.statusNotFound");
        }
        return $status;
    }
}
