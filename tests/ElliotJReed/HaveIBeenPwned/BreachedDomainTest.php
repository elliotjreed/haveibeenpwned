<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\BreachedDomain;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class BreachedDomainTest extends TestCase
{
    public function testItReturnsEmptyArrayIfNoBreachedAddressesOnDomain(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedDomain($client, 'fake-hibn-api-key'))->search('example.com');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breacheddomain/example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame([], $breaches);
    }

    public function testItReturnsEmptyArrayIfFourHundredAndFourResponse(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedDomain($client, 'fake-hibn-api-key'))->search('example.com');

        $this->assertSame([], $breaches);
    }

    public function testItReturnsBreachedAddressesOnDomain(): void
    {
        $response = '
          {
            "alias1": ["Adobe"],
            "alias2": ["Adobe", "Gawker", "Stratfor"]
          }
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedDomain($client, 'fake-hibn-api-key'))->search('example.com');

        $this->assertSame('/api/v3/breacheddomain/example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame([
            'alias1' => ['Adobe'],
            'alias2' => ['Adobe', 'Gawker', 'Stratfor']
        ], $breaches);
    }
}
