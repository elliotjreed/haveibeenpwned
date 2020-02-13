<?php

declare(strict_types=1);

namespace Tests\:namespace;

use PHPUnit\Framework\TestCase;
use :namespace\Greeting;

final class GreetingTest extends TestCase
{
    public function testItGreetsUser(): void
    {
        $greeting = new Greeting('Rasmus Lerdorf');

        $this->assertSame('Hello Rasmus Lerdorf', $greeting->sayHello());
    }
}
