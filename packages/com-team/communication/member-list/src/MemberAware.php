<?php

namespace Communication\MemberList;

interface MemberAware
{
    public function touchMember(int $profileId): void;
    public function removeMember(int $profileId): void;
}
