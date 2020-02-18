<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

use Exception;

final class InvalidEmail extends Exception
{
    protected $message = 'The email address specified is invalid according to RFC 822.';
}
