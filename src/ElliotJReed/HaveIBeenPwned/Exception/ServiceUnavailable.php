<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class ServiceUnavailable extends Exception implements HaveIBeenPwned
{
    protected $message = 'API unavailable.';

    public function __construct(?string $detail = null)
    {
        parent::__construct($detail ?? $this->message);
    }
}
