<?php

namespace User\Auth\Exception;

class LoginNameExistsException extends \Exception
{
    public function __construct(string $loginName)
    {
        parent::__construct('User with login name already exists: ' . $loginName);
    }
}
