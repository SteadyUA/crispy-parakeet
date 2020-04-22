<?php

namespace User\Auth\Exception;

class UnknownLoginNameException extends \Exception
{
    public function __construct(string $loginName)
    {
        parent::__construct('Unknown login name: ' . $loginName);
    }
}
