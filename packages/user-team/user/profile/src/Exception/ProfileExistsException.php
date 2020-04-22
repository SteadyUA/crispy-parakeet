<?php

namespace User\Profile\Exception;

class ProfileExistsException extends \Exception
{
    public function __construct($loginName)
    {
        parent::__construct('Profile exists: ' . $loginName);
    }
}
