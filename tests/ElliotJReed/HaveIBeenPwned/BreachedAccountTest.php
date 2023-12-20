<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\BreachedAccount;
use ElliotJReed\HaveIBeenPwned\DataClasses;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class BreachedAccountTest extends TestCase
{
    public function testItReturnsEmptyArrayForEmailAddressWithNoBreaches(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breaches('email@example.com');
        $this->assertSame([], $breaches);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsEmptyArrayIfFourHundredAndFourResponseReturnedForEmailAddress(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breaches('email@example.com');
        $this->assertSame([], $breaches);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsBreachesForEmailAddress(): void
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

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breaches('email@example.com');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false', $mock->getLastRequest()->getRequestTarget());
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

    public function testItReturnsEmptyArrayForEmailAddressWithNoBreachesExcludingUnverifiedBreaches(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breaches('email@example.com', false);
        $this->assertSame([], $breaches);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsBreachesForEmailAddressExcludingUnverifiedBreaches(): void
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

        $breach = (new BreachedAccount($client, 'fake-hibn-api-key'))->breaches('email@example.com', false)[0];

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
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

    public function testItReturnsEmptyArrayForEmailAddressWithNoBreachNames(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breachNames('email@example.com');

        $this->assertSame([], $breaches);
        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsEmptyArrayForFourHundredAndFourResponseForEmailAddressWithNoBreachNames(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breachNames('email@example.com');

        $this->assertSame([], $breaches);
        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsArrayOfBreachNamesForEmailAddress(): void
    {
        $response = '
          [
            {
              "Name":"Adobe"
            },
            {
              "Name":"BattlefieldHeroes"
            }
          ]
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breachNames('email@example.com');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(['Adobe', 'BattlefieldHeroes'], $breaches);
    }

    public function testItReturnsEmptyArrayForEmailAddressExcludingUnverifiedBreaches(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breaches('email@example.com', false);
        $this->assertSame([], $breaches);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsEmptyArrayForEmailAddressExcludingUnverifiedBreachesWithNoBreachNames(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breachNames('email@example.com', false);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame([], $breaches);
    }

    public function testItReturnsArrayOfBreachNamesForEmailAddressExcludingUnverifiedBreaches(): void
    {
        $response = '
          [
            {
              "Name":"Adobe"
            }
          ]
        ';

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new BreachedAccount($client, 'fake-hibn-api-key'))->breachNames('email@example.com', false);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(['Adobe'], $breaches);
    }

    public function testItReturnsZeroBreachesForEmailAddressWhenThereAreNoBreaches(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $count = (new BreachedAccount($client, 'fake-hibn-api-key'))->count('email@example.com');
        $this->assertSame(0, $count);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsZeroBreachesForFourHundredAndFourResponseForEmailAddress(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $count = (new BreachedAccount($client, 'fake-hibn-api-key'))->count('email@example.com');
        $this->assertSame(0, $count);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsNumberOfBreachesForEmailAddress(): void
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

        $breachCount = (new BreachedAccount($client, 'fake-hibn-api-key'))->count('email@example.com');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(2, $breachCount);
    }

    public function testItReturnsZeroBreachesForEmailAddressExcludingUnverifiedBreachesWhenThereAreNoBreaches(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $count = (new BreachedAccount($client, 'fake-hibn-api-key'))->count('email@example.com', false);
        $this->assertSame(0, $count);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);
    }

    public function testItReturnsNumberOfBreachesForEmailAddressExcludingUnverifiedBreaches(): void
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

        $breachCount = (new BreachedAccount($client, 'fake-hibn-api-key'))->count('email@example.com', false);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=true&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(2, $breachCount);
    }

    /**
     * @return void
     */
    public function dataProviderItReportsIfPasswordsWereBreached(): array
    {
        return [
            [DataClasses::DATA_CLASS_AVATARS, false],
            [DataClasses::DATA_CLASS_PASSWORDS, true],
        ];
    }

    /**
     * @dataProvider dataProviderItReportsIfPasswordsWereBreached
     */
    public function testItReportsIfPasswordsWereBreached(string $dataClass, bool $expected): void
    {
        $response = <<<END
[
  {
    "Name": "Adobe",
    "Title": "Adobe",
    "Domain": "adobe.com",
    "BreachDate": "2013-10-04",
    "AddedDate": "2013-12-04T00:00:00Z",
    "ModifiedDate": "2022-05-15T23:52:49Z",
    "PwnCount": 152445165,
    "Description": "In October 2013, 153 million Adobe accounts were breached with each containing an internal ID, username, email, <em>encrypted</em> password and a password hint in plain text. The password cryptography was poorly done and many were quickly resolved back to plain text. The unencrypted hints also <a href=\"http://www.troyhunt.com/2013/11/adobe-credentials-and-serious.html\" target=\"_blank\" rel=\"noopener\">disclosed much about the passwords</a> adding further to the risk that hundreds of millions of Adobe customers already faced.",
    "LogoPath": "https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png",
    "DataClasses": [
      "Email addresses",
      "Password hints",
      "Passwords",
      "Usernames"
    ],
    "IsVerified": true,
    "IsFabricated": false,
    "IsSensitive": false,
    "IsRetired": false,
    "IsSpamList": false,
    "IsMalware": false
  },
  {
    "Name": "Gawker",
    "Title": "Gawker",
    "Domain": "gawker.com",
    "BreachDate": "2010-12-11",
    "AddedDate": "2013-12-04T00:00:00Z",
    "ModifiedDate": "2013-12-04T00:00:00Z",
    "PwnCount": 1247574,
    "Description": "In December 2010, Gawker was attacked by the hacker collective &quot;Gnosis&quot; in retaliation for what was reported to be a feud between Gawker and 4Chan. Information about Gawkers 1.3M users was published along with the data from Gawker's other web presences including Gizmodo and Lifehacker. Due to the prevalence of password reuse, many victims of the breach <a href=\"http://www.troyhunt.com/2011/01/why-your-apps-security-design-could.html\" target=\"_blank\" rel=\"noopener\">then had their Twitter accounts compromised to send Acai berry spam</a>.",
    "LogoPath": "https://haveibeenpwned.com/Content/Images/PwnedLogos/Gawker.png",
    "DataClasses": [
      "Email addresses",
      "Passwords",
      "Usernames"
    ],
    "IsVerified": true,
    "IsFabricated": false,
    "IsSensitive": false,
    "IsRetired": false,
    "IsSpamList": false,
    "IsMalware": false
  },
  {
    "Name": "Stratfor",
    "Title": "Stratfor",
    "Domain": "stratfor.com",
    "BreachDate": "2011-12-24",
    "AddedDate": "2013-12-04T00:00:00Z",
    "ModifiedDate": "2013-12-04T00:00:00Z",
    "PwnCount": 859777,
    "Description": "In December 2011, &quot;Anonymous&quot; <a href=\"http://www.troyhunt.com/2011/12/5-website-security-lessons-courtesy-of.html\" target=\"_blank\" rel=\"noopener\">attacked the global intelligence company known as &quot;Stratfor&quot;</a> and consequently disclosed a veritable treasure trove of data including hundreds of gigabytes of email and tens of thousands of credit card details which were promptly used by the attackers to make charitable donations (among other uses). The breach also included 860,000 user accounts complete with email address, time zone, some internal system data and MD5 hashed passwords with no salt.",
    "LogoPath": "https://haveibeenpwned.com/Content/Images/PwnedLogos/Stratfor.png",
    "DataClasses": [
      "Credit cards",
      "Email addresses",
      "Names",
      "Passwords",
      "Phone numbers",
      "Physical addresses",
      "Usernames"
    ],
    "IsVerified": true,
    "IsFabricated": false,
    "IsSensitive": false,
    "IsRetired": false,
    "IsSpamList": false,
    "IsMalware": false
  }
]
END;

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $dataClassBreached = (new BreachedAccount($client, 'fake-hibn-api-key'))->isDataClassBreached('email@example.com', $dataClass, false);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame($expected, $dataClassBreached);
    }

    public function testItReportsLastDatePasswordsWereBreached(): void
    {
        $response = <<<END
[
  {
    "Name": "Adobe",
    "Title": "Adobe",
    "Domain": "adobe.com",
    "BreachDate": "2013-10-04",
    "AddedDate": "2013-12-04T00:00:00Z",
    "ModifiedDate": "2022-05-15T23:52:49Z",
    "PwnCount": 152445165,
    "Description": "In October 2013, 153 million Adobe accounts were breached with each containing an internal ID, username, email, <em>encrypted</em> password and a password hint in plain text. The password cryptography was poorly done and many were quickly resolved back to plain text. The unencrypted hints also <a href=\"http://www.troyhunt.com/2013/11/adobe-credentials-and-serious.html\" target=\"_blank\" rel=\"noopener\">disclosed much about the passwords</a> adding further to the risk that hundreds of millions of Adobe customers already faced.",
    "LogoPath": "https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png",
    "DataClasses": [
      "Email addresses",
      "Password hints",
      "Passwords",
      "Usernames"
    ],
    "IsVerified": true,
    "IsFabricated": false,
    "IsSensitive": false,
    "IsRetired": false,
    "IsSpamList": false,
    "IsMalware": false
  },
  {
    "Name": "Gawker",
    "Title": "Gawker",
    "Domain": "gawker.com",
    "BreachDate": "2010-12-11",
    "AddedDate": "2013-12-04T00:00:00Z",
    "ModifiedDate": "2013-12-04T00:00:00Z",
    "PwnCount": 1247574,
    "Description": "In December 2010, Gawker was attacked by the hacker collective &quot;Gnosis&quot; in retaliation for what was reported to be a feud between Gawker and 4Chan. Information about Gawkers 1.3M users was published along with the data from Gawker's other web presences including Gizmodo and Lifehacker. Due to the prevalence of password reuse, many victims of the breach <a href=\"http://www.troyhunt.com/2011/01/why-your-apps-security-design-could.html\" target=\"_blank\" rel=\"noopener\">then had their Twitter accounts compromised to send Acai berry spam</a>.",
    "LogoPath": "https://haveibeenpwned.com/Content/Images/PwnedLogos/Gawker.png",
    "DataClasses": [
      "Email addresses",
      "Passwords",
      "Usernames"
    ],
    "IsVerified": true,
    "IsFabricated": false,
    "IsSensitive": false,
    "IsRetired": false,
    "IsSpamList": false,
    "IsMalware": false
  },
  {
    "Name": "Stratfor",
    "Title": "Stratfor",
    "Domain": "stratfor.com",
    "BreachDate": "2011-12-24",
    "AddedDate": "2013-12-04T00:00:00Z",
    "ModifiedDate": "2013-12-04T00:00:00Z",
    "PwnCount": 859777,
    "Description": "In December 2011, &quot;Anonymous&quot; <a href=\"http://www.troyhunt.com/2011/12/5-website-security-lessons-courtesy-of.html\" target=\"_blank\" rel=\"noopener\">attacked the global intelligence company known as &quot;Stratfor&quot;</a> and consequently disclosed a veritable treasure trove of data including hundreds of gigabytes of email and tens of thousands of credit card details which were promptly used by the attackers to make charitable donations (among other uses). The breach also included 860,000 user accounts complete with email address, time zone, some internal system data and MD5 hashed passwords with no salt.",
    "LogoPath": "https://haveibeenpwned.com/Content/Images/PwnedLogos/Stratfor.png",
    "DataClasses": [
      "Credit cards",
      "Email addresses",
      "Names",
      "Passwords",
      "Phone numbers",
      "Physical addresses",
      "Usernames"
    ],
    "IsVerified": true,
    "IsFabricated": false,
    "IsSensitive": false,
    "IsRetired": false,
    "IsSpamList": false,
    "IsMalware": false
  }
]
END;

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $dataClassBreached = (new BreachedAccount($client, 'fake-hibn-api-key'))
            ->lastDataClassBreachDate('email@example.com', DataClasses::DATA_CLASS_PASSWORDS, false);

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('haveibeenpwned.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/api/v3/breachedaccount/email%40example.com?truncateResponse=false&?includeUnverified=false', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertEquals(new \DateTime("2013-10-04"), $dataClassBreached);
    }
}
