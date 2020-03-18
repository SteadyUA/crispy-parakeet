<?php

namespace Communication\Messages\Service;

use Communication\Messages\SendMessageAware;
use Communication\Messages\MessagesProjection;
use Communication\MemberList\MemberAware;
use User\Profile\ProfileService;

class Messages implements SendMessageAware, MessagesProjection
{
    /** @var MessagesStorage */
    private $storage;

    /** @var ProfileService */
    private $profileService;

    /** @var MemberAware */
    private $memberService;

    public function __construct(
        MessagesStorage $storage,
        ProfileService $profileService,
        MemberAware $memberService
    ) {
        $this->storage = $storage;
        $this->profileService = $profileService;
        $this->memberService = $memberService;
    }

    /**
     * @inheritDoc
     */
    public function sendMessage(int $profileId, string $messageText): int
    {
        $messageId = $this->storage->getNextId();
        $this->storage->addMessage($messageId, $profileId, $messageText);
        $this->memberService->touchMember($profileId);

        return $messageId;
    }

    public function latestMessages(int $limit): iterable
    {
        $amount = $limit;
        foreach ($this->storage->readMessages(null) as $message) {
            if ($amount <= 0) {
                break;
            }
            $amount --;
            $profile = $this->profileService->profileOf($message['profileId']);

            yield [
                'loginName' => $profile['loginName'] ?? '< unknown >',
                'messageText' => $message['messageText'],
                'createdAt' => $message['createdAt'],
            ];
        }
    }
}
