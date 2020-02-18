<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned\Double;

use ElliotJReed\HaveIBeenPwned\Api;
use Psr\Http\Message\StreamInterface;

final class ApiCallDummy extends Api
{
    public function mockCall(): StreamInterface
    {
        return $this->queryBreachApi('/dummy-api-path');
    }
}
