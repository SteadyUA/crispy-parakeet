<?php

namespace User\Profile\Service;

use Exception;
use User\Profile\ProfileService;

class Profile implements ProfileService
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(
        Storage $storage
    ) {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function create(string $loginName): int
    {
        $profile = $this->storage->findByLogin($loginName);
        if (isset($profile)) {
            throw new \User\Profile\Exception\ProfileExistsException($loginName);
        }
        $profileId = $this->storage->getNextId();
        $this->storage->save($profileId, $loginName);

        return $profileId;
    }

    public function profileOf(int $profileId): ?array
    {
        return $this->storage->findById($profileId);
    }

    public function idOfLogin(string $loginName): ?int
    {
        $profile = $this->storage->findByLogin($loginName);

        return $profile['profileId'] ?? null;
    }
}
