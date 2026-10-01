<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a request to the Hetzner Cloud API fails.
 */
class HetznerApiException extends RuntimeException
{
    public function __construct(string $message, protected int $status = 0)
    {
        parent::__construct($message);
    }

    /**
     * The HTTP status code returned by the Hetzner Cloud API, if any.
     */
    public function status(): int
    {
        return $this->status;
    }
}
