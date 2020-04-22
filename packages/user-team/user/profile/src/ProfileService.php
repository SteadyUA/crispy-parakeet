<?php

namespace User\Profile;

interface ProfileService
{
    /**
     * @param string $loginName
     * @return int profileId
     * @throws Exception\ProfileExistsException when exists
     */
    public function create(string $loginName): int;

    /**
     * @param int $profileId
     * @return array|null
     */
    public function profileOf(int $profileId): ?array;

    /**
     * @param string $loginName
     * @return int|null
     */
    public function idOfLogin(string $loginName): ?int;
}
