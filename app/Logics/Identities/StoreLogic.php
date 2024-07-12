<?php

namespace App\Logics\Identities;

use App\Kernel\Logics\StoreLogic as Logics;
use App\Models\Identities;

class StoreLogic extends Logics
{
    protected $errorCodeBase = 'inventory.identities.store';
    protected $autoInjectIdentity = false;
    public function __construct(
        protected Identities $identities
    ) {
        $this->model = $this->identities;
    }

    public function beforeSave(&$input)
    {
        if (!$this->getIdentityByName($input)) {
            return false;
        }
        return true;
    }

    public function getIdentityByName($input)
    {
        $identity = $this->identities->getIdentity($input['name']);
        if (!$identity) {
            return true;
        }
        return $this->errorCode("$this->errorCodeBase.identityAlreadyExist");
    }
}
