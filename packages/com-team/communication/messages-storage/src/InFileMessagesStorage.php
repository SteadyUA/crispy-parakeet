<?php

namespace Communication\MessagesStorage;

use Communication\Messages\Service\MessagesStorage;
use Phoenix\Util\InFileStorage\Dao;

class InFileMessagesStorage implements MessagesStorage
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

    public function addMessage(int $messageId, int $profileId, string $messageText): void
    {
        $message = [
            'messageId' => $messageId,
            'profileId' => $profileId,
            'messageText' => $messageText,
            'createdAt' => time()
        ];
        $this->dao->put($messageId, $message);
    }

    public function readMessages(int $fromMessageId = null): iterable
    {
        $read = !isset($fromMessageId);
        foreach ($this->dao->getReverseIterator() as $message) {
            if ($read) {
                yield $message;
            } else {
                $read = $fromMessageId == $message['messageId'];
            }
        }
    }
}
