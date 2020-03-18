<?php

namespace Communication\MemberList\Infrastructure;

use Communication\MemberList\Service\Storage;
use Phoenix\Util\InFileStorage\Dao;

class InFileStorage implements Storage
{
    /** @var Dao */
    private $dao;

    public function __construct(Dao $dao)
    {
        $this->dao = $dao;
    }

    public function update(int $profileId, string $time): void
    {
        $this->dao->put(
            $profileId,
            [
                'profileId' => $profileId,
                'updatedAt' => $time
            ]
        );
    }

    public function findAll(): iterable
    {
        yield from $this->dao->getIterator();
    }
}
