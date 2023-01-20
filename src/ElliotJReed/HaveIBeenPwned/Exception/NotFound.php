<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

final class NotFound extends \Exception implements HaveIBeenPwned
{
    protected $message = 'The email address could not be found and has therefore not been "pwned".';
}
