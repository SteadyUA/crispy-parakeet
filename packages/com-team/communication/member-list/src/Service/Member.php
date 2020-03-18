<?php

namespace Communication\MemberList\Service;

use Communication\MemberList\MemberAware;
use Communication\MemberList\MembersProjection;
use User\Profile\ProfileService;

class Member implements MemberAware, MembersProjection
{
    /** @var Storage */
    private $storage;

    /** @var ProfileService */
    private $profileService;

    public function __construct(
        Storage $storage,
        ProfileService $profileService
    ) {
        $this->storage = $storage;
        $this->profileService = $profileService;
    }

    public function touchMember(int $profileId): void
    {
        $this->storage->update($profileId, date('Y-m-d H:i:s'));
    }

    public function removeMember(int $profileId): void
    {
        $this->storage->update($profileId, '0000-00-00 00:00:00');
    }

    private function detectStatus(string $updatedAt): string
    {
        $updatedAt = strtotime($updatedAt);
        if ($updatedAt > strtotime('-1 minute')) {
            return 'active';
        }
        if ($updatedAt > strtotime('-5 minute')) {
            return 'away';
        }

        return 'inactive';
    }

    public function memberList(): iterable
    {
        foreach ($this->storage->findAll() as $member) {
            if ($member['updatedAt'] == '0000-00-00 00:00:00') {
                continue;
            }

            yield [
                'profileId' => $member['profileId'],
                'loginName' => $this->profileService->profileOf($member['profileId'])['loginName'] ?? '< unknown >',
                'updatedAt' => $member['updatedAt'],
                'status' => $this->detectStatus($member['updatedAt']),
            ];
        }
    }
}
