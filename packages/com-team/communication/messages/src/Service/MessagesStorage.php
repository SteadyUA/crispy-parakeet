<?php

namespace Communication\Messages\Service;

interface MessagesStorage
{
    public function getNextId(): int;
    public function addMessage(int $messageId, int $profileId, string $messageText): void;
    public function readMessages(int $fromMessageId = null): iterable;
}
