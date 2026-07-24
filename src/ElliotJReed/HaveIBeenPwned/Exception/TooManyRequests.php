<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class TooManyRequests extends Exception implements HaveIBeenPwned
{
    protected $message = 'API rate limit has been exceeded.';

    public function __construct(private readonly ?int $retryAfter = null)
    {
        parent::__construct($this->message);
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
