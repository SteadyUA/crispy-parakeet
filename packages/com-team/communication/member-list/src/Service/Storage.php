<?php

namespace Communication\MemberList\Service;

interface Storage
{
    public function update(int $profileId, string $time): void;
    public function findAll(): iterable;
}
