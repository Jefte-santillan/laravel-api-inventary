<?php

namespace App\Kernel\Exceptions;

use Exception;

class ContextException extends Exception
{
    protected array $context = [];

    public function setContext(array $context)
    {
        $this->context = $context;
        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
