<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use DateTime;
use ElliotJReed\HaveIBeenPwned\SubscribedDomains;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class SubscribedDomainsTest extends TestCase
{
    public function testItReturnsEmptyArrayIfNoSubscribedDomains(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $domains = (new SubscribedDomains($client, 'fake-hibn-api-key'))->all();

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/subscribeddomains', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame([], $domains);
    }

    public function testItReturnsSubscribedDomainsWithSearchHistory(): void
    {
        $response = '
          [
            {
              "DomainName": "example.com",
              "PwnCount": 120,
              "PwnCountExcludingSpamLists": 100,
              "PwnCountExcludingSpamListsAtLastSubscriptionRenewal": 90,
              "NextSubscriptionRenewal": "2027-01-01T00:00:00Z"
            }
          ]
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $domains = (new SubscribedDomains($client, 'fake-hibn-api-key'))->all();

        $domain = $domains[0];
        $this->assertSame('example.com', $domain->getDomainName());
        $this->assertSame(120, $domain->getPwnCount());
        $this->assertSame(100, $domain->getPwnCountExcludingSpamLists());
        $this->assertSame(90, $domain->getPwnCountExcludingSpamListsAtLastSubscriptionRenewal());
        $this->assertEquals(new DateTime('2027-01-01T00:00:00Z'), $domain->getNextSubscriptionRenewal());
    }

    public function testItReturnsSubscribedDomainsWithNoSearchHistory(): void
    {
        $response = '
          [
            {
              "DomainName": "never-searched.com"
            }
          ]
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $domains = (new SubscribedDomains($client, 'fake-hibn-api-key'))->all();

        $domain = $domains[0];
        $this->assertSame('never-searched.com', $domain->getDomainName());
        $this->assertNull($domain->getPwnCount());
        $this->assertNull($domain->getPwnCountExcludingSpamLists());
        $this->assertNull($domain->getPwnCountExcludingSpamListsAtLastSubscriptionRenewal());
        $this->assertNull($domain->getNextSubscriptionRenewal());
    }
}
