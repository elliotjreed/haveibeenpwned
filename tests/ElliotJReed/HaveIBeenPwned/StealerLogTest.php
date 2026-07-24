<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\StealerLog;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class StealerLogTest extends TestCase
{
    public function testItReturnsEmptyArrayIfNoStealerLogDomainsForEmail(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $domains = (new StealerLog($client, 'fake-hibn-api-key'))->byEmail('jane@gmail.com');

        $this->assertSame([], $domains);
    }

    public function testItReturnsStealerLogDomainsForEmail(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '["netflix.com", "spotify.com"]')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $domains = (new StealerLog($client, 'fake-hibn-api-key'))->byEmail('jane@gmail.com');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('/api/v3/stealerlogsbyemail/jane%40gmail.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(['netflix.com', 'spotify.com'], $domains);
    }

    public function testItReturnsEmptyArrayIfNoStealerLogEmailsForWebsiteDomain(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $emails = (new StealerLog($client, 'fake-hibn-api-key'))->byWebsiteDomain('netflix.com');

        $this->assertSame([], $emails);
    }

    public function testItReturnsStealerLogEmailsForWebsiteDomain(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '["andy@gmail.com", "jane@gmail.com"]')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $emails = (new StealerLog($client, 'fake-hibn-api-key'))->byWebsiteDomain('netflix.com');

        $this->assertSame('/api/v3/stealerlogsbywebsitedomain/netflix.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame(['andy@gmail.com', 'jane@gmail.com'], $emails);
    }

    public function testItReturnsEmptyArrayIfNoStealerLogAliasesForEmailDomain(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $aliases = (new StealerLog($client, 'fake-hibn-api-key'))->byEmailDomain('gmail.com');

        $this->assertSame([], $aliases);
    }

    public function testItReturnsStealerLogAliasesForEmailDomain(): void
    {
        $response = '{"andy": ["netflix.com"], "jane": ["netflix.com", "spotify.com"]}';
        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $aliases = (new StealerLog($client, 'fake-hibn-api-key'))->byEmailDomain('gmail.com');

        $this->assertSame('/api/v3/stealerlogsbyemaildomain/gmail.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame([
            'andy' => ['netflix.com'],
            'jane' => ['netflix.com', 'spotify.com']
        ], $aliases);
    }
}
