<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class NotFound extends Exception implements HaveIBeenPwned
{
    protected $message = 'The email address could not be found and has therefore not been "pwned".';

    public function __construct(?string $detail = null)
    {
        parent::__construct($detail ?? $this->message);
    }
}
