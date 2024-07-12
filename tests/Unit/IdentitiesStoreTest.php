<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Logics\Identities\StoreLogic;


class IdentitiesStoreTest extends TestCase
{
    /**
     * @test
     * @group done
     * @group identities.store.success
     */
    public function success()
    {
        $input = ['name' => 'Abdiel Santillan'];
        $result =  app(StoreLogic::class)->run($input);
        dd($result, app('messages'));
    }
}
