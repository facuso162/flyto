<?php

namespace App\Shared\Http;

use Exception;

class HttpException extends Exception
{
    private array $details;

    public function __construct(string $message, int $statusCode = 500, array $details = [])
    {
        parent::__construct($message, $statusCode);

        $this->details = $details;
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
