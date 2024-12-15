<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class UnknownServerError extends Exception implements HaveIBeenPwned
{
    protected $message = 'An unexpected HTTP response code was returned from the HIBP API.';
}
