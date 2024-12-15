<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class TooManyRequests extends Exception implements HaveIBeenPwned
{
    protected $message = 'API rate limit has been exceeded.';
}
