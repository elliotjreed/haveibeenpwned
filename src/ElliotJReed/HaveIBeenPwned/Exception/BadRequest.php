<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

final class BadRequest extends \Exception implements HaveIBeenPwned
{
    protected $message = 'The provided email address does not comply with an acceptable format.';
}
