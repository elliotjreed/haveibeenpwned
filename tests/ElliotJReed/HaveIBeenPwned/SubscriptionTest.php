<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use DateTime;
use ElliotJReed\HaveIBeenPwned\Subscription;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class SubscriptionTest extends TestCase
{
    public function testItReturnsSubscriptionStatus(): void
    {
        $response = '
          {
            "SubscriptionName": "Pro 2",
            "Description": "Pro subscription, up to 2,500,000 breached identities per domain",
            "SubscribedUntil": "2027-01-01T00:00:00Z",
            "Rpm": 50,
            "DomainSearchMaxBreachedAccounts": 2500000,
            "MaxBreachedDomains": null,
            "IncludesStealerLogs": true,
            "IncludesBulkDomainAdd": true,
            "IncludesAutoSubdomainVerification": true,
            "IncludesCustomerDomains": false,
            "IncludesKAnon": true
          }
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $status = (new Subscription($client, 'fake-hibn-api-key'))->status();

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/subscription/status', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame('Pro 2', $status->getSubscriptionName());
        $this->assertSame('Pro subscription, up to 2,500,000 breached identities per domain', $status->getDescription());
        $this->assertEquals(new DateTime('2027-01-01T00:00:00Z'), $status->getSubscribedUntil());
        $this->assertSame(50, $status->getRpm());
        $this->assertSame(2500000, $status->getDomainSearchMaxBreachedAccounts());
        $this->assertNull($status->getMaxBreachedDomains());
        $this->assertTrue($status->includesStealerLogs());
        $this->assertTrue($status->includesBulkDomainAdd());
        $this->assertTrue($status->includesAutoSubdomainVerification());
        $this->assertFalse($status->includesCustomerDomains());
        $this->assertTrue($status->includesKAnon());
    }

    public function testItReturnsSubscriptionStatusWithMaxBreachedDomains(): void
    {
        $response = '
          {
            "SubscriptionName": "Pro",
            "Description": "Pro subscription",
            "SubscribedUntil": "2027-01-01T00:00:00Z",
            "Rpm": 10,
            "DomainSearchMaxBreachedAccounts": 100000,
            "MaxBreachedDomains": 5,
            "IncludesStealerLogs": false,
            "IncludesBulkDomainAdd": false,
            "IncludesAutoSubdomainVerification": false,
            "IncludesCustomerDomains": false,
            "IncludesKAnon": false
          }
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $status = (new Subscription($client, 'fake-hibn-api-key'))->status();

        $this->assertSame(5, $status->getMaxBreachedDomains());
    }
}
