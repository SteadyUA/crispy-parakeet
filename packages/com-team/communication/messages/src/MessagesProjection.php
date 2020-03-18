<?php

namespace Communication\Messages;

interface MessagesProjection
{
    public function latestMessages(int $limit): iterable;
}
