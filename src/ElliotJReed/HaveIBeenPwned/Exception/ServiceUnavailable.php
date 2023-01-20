<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Exception;

final class ServiceUnavailable extends \Exception implements HaveIBeenPwned
{
    protected $message = 'API unavailable.';
}
