<?php

namespace Communication\MemberList;

interface MembersProjection
{
    public function memberList(): iterable;
}
