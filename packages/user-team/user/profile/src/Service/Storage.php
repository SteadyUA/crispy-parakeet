<?php

namespace User\Profile\Service;

interface Storage
{
    public function getNextId(): int;
    public function findById(int $profileId): ?array;
    public function findByLogin(string $loginName): ?array;
    public function save(int $profileId, string $loginName): void;
}
