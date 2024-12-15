<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class Unauthorised extends Exception implements HaveIBeenPwned
{
    protected $message = 'API key is invalid.';
}
