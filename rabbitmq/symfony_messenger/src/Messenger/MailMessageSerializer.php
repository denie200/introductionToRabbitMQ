<?php

declare(strict_types=1);

namespace App\Messenger;

use App\Message\MessageInterface;
use App\Message\MailMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MailMessageSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        $data = json_decode($body, true);

        // in case of redelivery, unserialize any stamps
        $stamps = [];

        if(isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        $message = new MailMessage(
            (array) $data['recipient'],
            $data['html'],
            $data['text'],
            $data['headers']
        );

        if(!$message->isValid()) {
            throw new MessageDecodingFailedException(
                implode("\n", $message->getErrors()),
                500
            );
        }

        return new Envelope($message, $stamps);
    }

    /**
     * @return array<string, array<string, string>>|array<string, string>|false[]
     */
    public function encode(Envelope $envelope): array
    {
        // this is called if a message is redelivered for "retry"
        $message = $envelope->getMessage();

        if (
            $message instanceof MessageInterface
        ) {
            if(!$message->isValid()) {
                throw new \Exception(
                    sprintf('ExternalMessageError: The message %s is not valid!', get_class($message)),
                    500
                );
            }

            $data = $message->getBody();
        } else {
            throw new \Exception('Unsupported message class. Message must implent the MessageInterface.');
        }

        $allStamps = [];

        foreach ($envelope->all() as $stamps) {
            $allStamps = \array_merge($allStamps, $stamps);
        }

        return [
            'body' => \json_encode($data),
            'headers' => [
                // store stamps as a header - to be read in decode()
                'stamps' => \serialize($allStamps),
            ],
        ];
    }
}
