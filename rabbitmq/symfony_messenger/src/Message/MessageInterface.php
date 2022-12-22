<?php

declare(strict_types=1);

namespace App\Message;

interface MessageInterface
{
    public function isValid(): bool;

    /**
     * @return mixed[]
     */
    public function getBody(): array;
}
