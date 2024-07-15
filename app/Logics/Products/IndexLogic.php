<?php

namespace App\Logics\Products;

use App\Flow\Logics\ModelQuery\IndexLogic as Logic;

class IndexLogic
{
    public function __construct(protected Logic $logic)
    {
    }

    public function run($input)
    {
        $queryData = [
            'model' => 'App\\Models\\Products',
            'select' => ['products.*'],
            'page' => $input['page'] ?? 1,
            'limit' => $input['limit'] ?? 15,

        ];
        if (isset($input['query'])) {
            $queryData['value'] = $input['query'];
            $queryData['scope'] = 'search';
        }
        return $this->logic->run($queryData);
    }
}
