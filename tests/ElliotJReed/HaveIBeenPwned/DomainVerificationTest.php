<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\DomainVerification;
use ElliotJReed\HaveIBeenPwned\Exception\BadRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class DomainVerificationTest extends TestCase
{
    public function testItGeneratesDnsToken(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"txtRecordValue":"hibp-verify=dweb_abc123xyz"}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $token = (new DomainVerification($client, 'fake-hibn-api-key'))->generateDnsToken('example.com');

        $this->assertSame('POST', $mock->getLastRequest()->getMethod());
        $this->assertSame('/api/v3/domainverification/generatednstoken', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('{"DomainName":"example.com"}', (string) $mock->getLastRequest()->getBody());
        $this->assertSame(['fake-hibn-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame('hibp-verify=dweb_abc123xyz', $token);
    }

    public function testItReturnsNullWhenDnsTokenVerificationSucceeds(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $message = (new DomainVerification($client, 'fake-hibn-api-key'))->verifyDnsToken('example.com');

        $this->assertSame('POST', $mock->getLastRequest()->getMethod());
        $this->assertSame('/api/v3/domainverification/verifydnstoken', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('{"DomainName":"example.com"}', (string) $mock->getLastRequest()->getBody());

        $this->assertNull($message);
    }

    public function testItReturnsFailureMessageWhenDnsTokenVerificationFails(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"message":"No hibp-verify TXT records were found on the domain."}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $message = (new DomainVerification($client, 'fake-hibn-api-key'))->verifyDnsToken('example.com');

        $this->assertSame('No hibp-verify TXT records were found on the domain.', $message);
    }

    public function testItSendsVerificationEmail(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        (new DomainVerification($client, 'fake-hibn-api-key'))->sendEmail('example.com', 'admin');

        $this->assertSame('POST', $mock->getLastRequest()->getMethod());
        $this->assertSame('/api/v3/domainverification/sendemail', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('{"DomainName":"example.com","EmailAlias":"admin"}', (string) $mock->getLastRequest()->getBody());
    }

    public function testItThrowsBadRequestExceptionForInvalidAlias(): void
    {
        $mock = new MockHandler([
            new Response(400, [], '{"message":"The emailAlias field did not contain a valid value."}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(BadRequest::class);

        (new DomainVerification($client, 'fake-hibn-api-key'))->sendEmail('example.com', 'not-a-valid-alias');
    }
}
