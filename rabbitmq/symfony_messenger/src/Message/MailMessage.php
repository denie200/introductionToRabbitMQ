<?php

namespace App\Message;

use App\Message\MessageInterface;

/**
 * This message class contains the structure of an email message.
 */
final class MailMessage implements MessageInterface
{
    private array $errors = [];

    public function __construct(
        private readonly array $recipient = [],
        private readonly string $html = '',
        private readonly string $text = '',
        private readonly array $headers = []
    ) {}

    public function isValid(): bool
    {
        $this->errors = [];

        if( !is_array($this->recipient) ||
            count($this->recipient) === 0 )
        {
            $this->errors[] = "Recipient is not an array with items.";
        } else {
            foreach($this->recipient as $email) {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = sprintf(
                        "Recipient %s is not a valid email address",
                        $email
                    );
                }
            }
        }

        if(empty($this->html) && empty($text)) {
            $this->errors[] = "The message has no content! Set the html or text property to a non-empty string.";
        }

        if(count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    public function getBody(): array
    {
        return [
            'recipient' => $this->recipient,
            'headers' => $this->headers,
            'text' => $this->text,
            'html' => $this->html
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
