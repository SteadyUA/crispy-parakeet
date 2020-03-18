<?php

namespace User\Profile\Infrastructure;

use User\Profile\Service\Storage;
use Phoenix\Util\InFileStorage\Dao;

class InFileStorage implements Storage
{
    /** @var Dao */
    private $dao;

    public function __construct(Dao $dao)
    {
        $this->dao = $dao;
    }

    public function getNextId(): int
    {
        return $this->dao->nextId();
    }

    public function findById(int $profileId): ?array
    {
        return $this->dao->get($profileId);
    }

    public function findByLogin(string $loginName): ?array
    {
        foreach ($this->dao->getIterator() as $data) {
            if ($data['loginName'] == $loginName) {
                return $data;
            }
        }

        return null;
    }

    public function save(int $profileId, string $loginName): void
    {
        $data = [
            'profileId' => $profileId,
            'loginName' => $loginName
        ];
        $this->dao->put($profileId, $data);
    }
}
