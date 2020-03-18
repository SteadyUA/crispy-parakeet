<?php

namespace Communication\Messages;

interface SendMessageAware
{
    /**
     * @param int $profileId
     * @param string $messageText
     * @return int messageId
     */
    public function sendMessage(int $profileId, string $messageText): int;
}
