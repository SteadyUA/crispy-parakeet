<?php

namespace User\Auth;

use Exception;

interface AuthService
{
    /**
     * @param string $loginName
     * @return int profileId
     * @throws Exception
     */
    public function signIn(string $loginName): int;

    /**
     * @param string $loginName
     * @return int profileId
     * @throws Exception
     */
    public function logIn(string $loginName): int;

    /**
     * @param int $profileId
     */
    public function logOut(int $profileId): void;
}
