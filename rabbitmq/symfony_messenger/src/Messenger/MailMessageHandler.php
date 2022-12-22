<?php

namespace App\Messenger;

use App\Message\MailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MailMessageHandler implements MessageHandlerInterface
{
    public function __invoke(MailMessage $message): void
    {
        var_dump($message);
    }
}
