<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class BadRequest extends Exception implements HaveIBeenPwned
{
    protected $message = 'The provided email address does not comply with an acceptable format.';

    public function __construct(?string $detail = null)
    {
        parent::__construct($detail ?? $this->message);
    }
}
