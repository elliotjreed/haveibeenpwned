<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Breaches;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class BreachesTest extends TestCase
{
    public function testItReturnsEmptyArrayIfNoBreaches(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new Breaches($client, 'fake-hibn-api-key'))->allSources();

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getRequestTarget());
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

        $breaches = (new Breaches($client, 'fake-hibn-api-key'))->allSources();

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame([], $breaches);
    }

    public function testItGetsAllBreaches(): void
    {
        $response = '
          [
            {
              "Name":"Adobe",
              "Title":"Adobe",
              "Domain":"adobe.com",
              "BreachDate":"2013-10-04",
              "AddedDate":"2013-12-04T00:00Z",
              "ModifiedDate":"2013-12-04T00:00Z",
              "PwnCount":152445165,
              "Description":"In October 2013...",
              "DataClasses":[
                "Email addresses",
                "Password hints",
                "Passwords",
                "Usernames"
              ],
              "IsVerified":true,
              "IsFabricated":false,
              "IsSensitive":false,
              "IsRetired":false,
              "IsSpamList":false,
              "LogoPath":"https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png"
            },
            {
              "Name":"BattlefieldHeroes",
              "Title":"Battlefield Heroes",
              "Domain":"battlefieldheroes.com",
              "BreachDate":"2011-06-26",
              "AddedDate":"2014-01-23T13:10Z",
              "ModifiedDate":"2014-01-23T13:10Z",
              "PwnCount":530270,
              "Description":"In June 2011...",
              "DataClasses":[
                "Passwords",
                "Usernames"
              ],
              "IsVerified":true,
              "IsFabricated":false,
              "IsSensitive":false,
              "IsRetired":false,
              "IsSpamList":false,
              "LogoPath":"https://haveibeenpwned.com/Content/Images/PwnedLogos/BattlefieldHeroes.png"
            }
          ]
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new Breaches($client, 'fake-hibn-api-key'))->allSources();

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $firstBreach = $breaches[0];
        $this->assertSame('Adobe', $firstBreach->getName());
        $this->assertSame('Adobe', $firstBreach->getTitle());
        $this->assertSame('adobe.com', $firstBreach->getDomain());
        $this->assertEquals(new \DateTime('2013-10-04 00:00:00'), $firstBreach->getBreachDate());
        $this->assertEquals(new \DateTime('2013-12-04T00:00Z'), $firstBreach->getAddedDate());
        $this->assertEquals(new \DateTime('2013-12-04T00:00Z'), $firstBreach->getModifiedDate());
        $this->assertSame(152445165, $firstBreach->getPwnCount());
        $this->assertSame('In October 2013...', $firstBreach->getDescription());
        $this->assertSame(['Email addresses', 'Password hints', 'Passwords', 'Usernames'], $firstBreach->getDataClasses());
        $this->assertTrue($firstBreach->isVerified());
        $this->assertFalse($firstBreach->isFabricated());
        $this->assertFalse($firstBreach->isSensitive());
        $this->assertFalse($firstBreach->isRetired());
        $this->assertFalse($firstBreach->IsSpamList());
        $this->assertSame('https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png', $firstBreach->getLogoPath());

        $secondBreach = $breaches[1];
        $this->assertSame('BattlefieldHeroes', $secondBreach->getName());
        $this->assertSame('Battlefield Heroes', $secondBreach->getTitle());
        $this->assertSame('battlefieldheroes.com', $secondBreach->getDomain());
        $this->assertEquals(new \DateTime('2011-06-26 00:00:00'), $secondBreach->getBreachDate());
        $this->assertEquals(new \DateTime('2014-01-23T13:10Z'), $secondBreach->getAddedDate());
        $this->assertEquals(new \DateTime('2014-01-23T13:10Z'), $secondBreach->getModifiedDate());
        $this->assertSame(530270, $secondBreach->getPwnCount());
        $this->assertSame('In June 2011...', $secondBreach->getDescription());
        $this->assertSame(['Passwords', 'Usernames'], $secondBreach->getDataClasses());
        $this->assertTrue($secondBreach->isVerified());
        $this->assertFalse($secondBreach->isFabricated());
        $this->assertFalse($secondBreach->isSensitive());
        $this->assertFalse($secondBreach->isRetired());
        $this->assertFalse($secondBreach->IsSpamList());
        $this->assertSame('https://haveibeenpwned.com/Content/Images/PwnedLogos/BattlefieldHeroes.png', $secondBreach->getLogoPath());
    }

    public function testItReturnsEmptyArrayWhenThereAreNoBreachesForWebsite(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new Breaches($client, 'fake-hibn-api-key'))->byDomain('adobe.com');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('domain=adobe.com', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breaches?domain=adobe.com', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame([], $breaches);
    }

    public function testItReturnsEmptyArrayIfFourHundredAndFourResponseForBreachesForWebsite(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new Breaches($client, 'fake-hibn-api-key'))->byDomain('adobe.com');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('domain=adobe.com', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breaches?domain=adobe.com', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame([], $breaches);
    }

    public function testItGetsAllBreachesForWebsite(): void
    {
        $response = '
          [
            {
              "Name":"Adobe",
              "Title":"Adobe",
              "Domain":"adobe.com",
              "BreachDate":"2013-10-04",
              "AddedDate":"2013-12-04T00:00Z",
              "ModifiedDate":"2013-12-04T00:00Z",
              "PwnCount":152445165,
              "Description":"In October 2013...",
              "DataClasses":[
                "Email addresses",
                "Password hints",
                "Passwords",
                "Usernames"
              ],
              "IsVerified":true,
              "IsFabricated":false,
              "IsSensitive":false,
              "IsRetired":false,
              "IsSpamList":false,
              "LogoPath":"https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png"
            }
          ]
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breach = (new Breaches($client, 'fake-hibn-api-key'))->byDomain('adobe.com')[0];

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breaches', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('domain=adobe.com', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breaches?domain=adobe.com', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame('Adobe', $breach->getName());
        $this->assertSame('Adobe', $breach->getTitle());
        $this->assertSame('adobe.com', $breach->getDomain());
        $this->assertEquals(new \DateTime('2013-10-04 00:00:00'), $breach->getBreachDate());
        $this->assertEquals(new \DateTime('2013-12-04T00:00Z'), $breach->getAddedDate());
        $this->assertEquals(new \DateTime('2013-12-04T00:00Z'), $breach->getModifiedDate());
        $this->assertSame(152445165, $breach->getPwnCount());
        $this->assertSame('In October 2013...', $breach->getDescription());
        $this->assertSame(['Email addresses', 'Password hints', 'Passwords', 'Usernames'], $breach->getDataClasses());
        $this->assertTrue($breach->isVerified());
        $this->assertFalse($breach->isFabricated());
        $this->assertFalse($breach->isSensitive());
        $this->assertFalse($breach->isRetired());
        $this->assertFalse($breach->IsSpamList());
        $this->assertSame('https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png', $breach->getLogoPath());
    }

    public function testItReturnsNullIfThereIsNoBreachByName(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breach = (new Breaches($client, 'fake-hibn-api-key'))->bySourceName('Adobe');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breach/adobe', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breach/adobe', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertNull($breach);
    }

    public function testItReturnsNullIfFourHundredAndFourResponseFoBreachName(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breach = (new Breaches($client, 'fake-hibn-api-key'))->bySourceName('Adobe');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breach/adobe', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breach/adobe', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertNull($breach);
    }

    public function testItGetsBreachByName(): void
    {
        $response = '
          {
            "Name":"Adobe",
            "Title":"Adobe",
            "Domain":"adobe.com",
            "BreachDate":"2013-10-04",
            "AddedDate":"2013-12-04T00:00Z",
            "ModifiedDate":"2013-12-04T00:00Z",
            "PwnCount":152445165,
            "Description":"In October 2013...",
            "DataClasses":[
              "Email addresses",
              "Password hints",
              "Passwords",
              "Usernames"
            ],
            "IsVerified":true,
            "IsFabricated":false,
            "IsSensitive":false,
            "IsRetired":false,
            "IsSpamList":false,
            "LogoPath":"https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png"
          }
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breach = (new Breaches($client, 'fake-hibn-api-key'))->bySourceName('Adobe');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breach/adobe', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breach/adobe', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame('Adobe', $breach->getName());
        $this->assertSame('Adobe', $breach->getTitle());
        $this->assertSame('adobe.com', $breach->getDomain());
        $this->assertEquals(new \DateTime('2013-10-04 00:00:00'), $breach->getBreachDate());
        $this->assertEquals(new \DateTime('2013-12-04T00:00Z'), $breach->getAddedDate());
        $this->assertEquals(new \DateTime('2013-12-04T00:00Z'), $breach->getModifiedDate());
        $this->assertSame(152445165, $breach->getPwnCount());
        $this->assertSame('In October 2013...', $breach->getDescription());
        $this->assertSame(['Email addresses', 'Password hints', 'Passwords', 'Usernames'], $breach->getDataClasses());
        $this->assertTrue($breach->isVerified());
        $this->assertFalse($breach->isFabricated());
        $this->assertFalse($breach->isSensitive());
        $this->assertFalse($breach->isRetired());
        $this->assertFalse($breach->IsSpamList());
        $this->assertSame('https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png', $breach->getLogoPath());
    }
}
