<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\DataClasses;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class DataClassesTest extends TestCase
{
    public function testItGetsAllDataClasses(): void
    {
        $response = '["Account balances","Address book contacts","Age groups","Ages","Apps installed on devices"]';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $dataClasses = (new DataClasses($client, 'fake-hibn-api-key'))->all();

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/dataclasses', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/dataclasses', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(['Account balances', 'Address book contacts', 'Age groups', 'Ages', 'Apps installed on devices'], $dataClasses);
    }
}
