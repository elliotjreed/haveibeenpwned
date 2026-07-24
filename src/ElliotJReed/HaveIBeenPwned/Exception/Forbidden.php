<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class Forbidden extends Exception implements HaveIBeenPwned
{
    protected $message = 'No user agent has been specified in the request.';

    public function __construct(?string $detail = null)
    {
        parent::__construct($detail ?? $this->message);
    }
}
