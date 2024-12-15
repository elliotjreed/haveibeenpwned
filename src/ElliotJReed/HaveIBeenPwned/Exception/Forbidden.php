<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class Forbidden extends Exception implements HaveIBeenPwned
{
    protected $message = 'No user agent has been specified in the request.';
}
