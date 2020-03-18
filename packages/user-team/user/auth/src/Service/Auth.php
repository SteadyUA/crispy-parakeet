<?php

namespace User\Auth\Service;

use Exception;
use Communication\MemberList\MemberAware;
use User\Auth\AuthService;
use User\Profile\ProfileService;

class Auth implements AuthService
{
    /**
     * @var ProfileService
     */
    private $profileService;

    /** @var MemberAware */
    private $memberService;

    public function __construct(
        MemberAware $memberService,
        ProfileService $profileService
    ) {
        $this->memberService = $memberService;
        $this->profileService = $profileService;
    }

    /**
     * @inheritDoc
     */
    public function signIn(string $loginName): int
    {
        try {
            $profileId = $this->profileService->create($loginName);
        } catch (Exception $ex) {
            throw new Exception('Login name in use.');
        }

        $this->memberService->touchMember($profileId);

        return $profileId;
    }

    /**
     * @inheritDoc
     */
    public function logIn(string $loginName): int
    {
        $profileId = $this->profileService->idOfLogin($loginName);
        if (null === $profileId) {
            throw new Exception('Unknown login name.');
        }

        $this->memberService->touchMember($profileId);

        return $profileId;
    }

    /**
     * @inheritDoc
     */
    public function logOut(int $profileId): void
    {
        $this->memberService->removeMember($profileId);
    }
}
