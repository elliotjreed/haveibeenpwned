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

    public function mockCallWithHeaders(array $headers): StreamInterface
    {
        return $this->queryBreachApi('/dummy-api-path', headers: $headers);
    }

    public function mockPostCall(array $body): StreamInterface
    {
        return $this->postToBreachApi('/dummy-api-path', $body);
    }
}
