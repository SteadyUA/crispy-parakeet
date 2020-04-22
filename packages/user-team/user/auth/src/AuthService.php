<?php

namespace User\Auth;

interface AuthService
{
    /**
     * @param string $loginName
     * @return int profileId
     * @throws Exception\LoginNameExistsException
     */
    public function signIn(string $loginName): int;

    /**
     * @param string $loginName
     * @return int profileId
     * @throws Exception\UnknownLoginNameException
     */
    public function logIn(string $loginName): int;

    /**
     * @param int $profileId
     */
    public function logOut(int $profileId): void;
}
