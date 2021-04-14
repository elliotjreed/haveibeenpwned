<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Password;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class PasswordTest extends TestCase
{
    public function testItReturnsEmptyArrayIfNoBreaches(): void
    {
        $responseBody = '00CADBF0C837E81197E29D51600DD960581:1' . "\r\n" .
            '019C9F9C0115E698B963D848275DB63ACD5:2' . "\r\n" .
            '025BCF35BC2EC1E9321D184A379D519DCED:1' . "\r\n" .
            '027E75A76CFD76CDFBE22F94464F5E5CE63:1' . "\r\n" .
            '02982E1B44352E26BAC9BE868FED4F567AE:2' . "\r\n" .
            '04DA3771F319B27CA5923FE55F77DAD2A3D:1';
        $mock = new MockHandler([
            new Response(200, [], $responseBody)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new Password($client, 'fake-hibn-api-key'))->count('a-really-really-secure-correct-horse-battery-staple');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('api.pwnedpasswords.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/range/4F8A4', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/range/4F8A4', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(0, $breaches);
    }

    public function testItReturnsCountOfBreaches(): void
    {
        $responseBody = 'C4659DBB38B9B34CF45C54C9EE60AEE0F1A:16' . "\r\n" .
            'C536D69F5256CDAC372B76E644DB604547D:1' . "\r\n" .
            'C565F02AFBA54DFD1AAF12AD20473FB9C7C:2' . "\r\n" .
            'C5A8C49CD024B82E517C98732CFB0F8A23C:1' . "\r\n" .
            'C5DE2D08BADD5F48360C7D9CFAFEF4512EB:2' . "\r\n" .
            'C6008F9CAB4083784CBD1874F76618D2A97:121251';
        $mock = new MockHandler([
            new Response(200, [], $responseBody)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $breaches = (new Password($client, 'fake-hibn-api-key'))->count('password123');

        $this->assertSame('GET', $mock->getLastRequest()->getMethod());
        $this->assertSame('https', $mock->getLastRequest()->getUri()->getScheme());
        $this->assertSame('api.pwnedpasswords.com', $mock->getLastRequest()->getUri()->getHost());
        $this->assertSame('/range/CBFDA', $mock->getLastRequest()->getUri()->getPath());
        $this->assertSame('', $mock->getLastRequest()->getUri()->getQuery());
        $this->assertSame('/range/CBFDA', $mock->getLastRequest()->getRequestTarget());
        $this->assertSame(['hibp-php'], $mock->getLastRequest()->getHeaders()['user-agent']);

        $this->assertSame(121251, $breaches);
    }
}
