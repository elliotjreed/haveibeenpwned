<?php

declare(strict_types=1);

namespace Tests\ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Exception\BadRequest;
use ElliotJReed\HaveIBeenPwned\Exception\Forbidden;
use ElliotJReed\HaveIBeenPwned\Exception\NotFound;
use ElliotJReed\HaveIBeenPwned\Exception\ServiceUnavailable;
use ElliotJReed\HaveIBeenPwned\Exception\TooManyRequests;
use ElliotJReed\HaveIBeenPwned\Exception\Unauthorised;
use ElliotJReed\HaveIBeenPwned\Exception\UnknownServerError;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\ElliotJReed\HaveIBeenPwned\Double\ApiCallDummy;

final class ApiTest extends TestCase
{
    public function testItReturnsResponseBodyIfStatusIsTwoHundred(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $response = (new ApiCallDummy($client, 'fake-api-key'))->mockCall();

        $this->assertSame('response body', $response->getContents());
    }

    public function testItSendsExtraHeadersOnGetRequests(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        (new ApiCallDummy($client, 'fake-api-key'))->mockCallWithHeaders(['Add-Padding' => 'true']);

        $this->assertSame(['true'], $mock->getLastRequest()->getHeaders()['Add-Padding']);
        $this->assertSame(['fake-api-key'], $mock->getLastRequest()->getHeaders()['hibp-api-key']);
    }

    public function testItSendsJsonBodyOnPostRequests(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $response = (new ApiCallDummy($client, 'fake-api-key'))->mockPostCall(['DomainName' => 'example.com']);

        $this->assertSame('POST', $mock->getLastRequest()->getMethod());
        $this->assertSame('{"DomainName":"example.com"}', (string) $mock->getLastRequest()->getBody());
        $this->assertSame(['application/json'], $mock->getLastRequest()->getHeaders()['Content-Type']);
        $this->assertSame('response body', $response->getContents());
    }

    public function testItThrowsBadRequestExceptionIfStatusIsFourHundred(): void
    {
        $mock = new MockHandler([
            new Response(400, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage('The provided email address does not comply with an acceptable format.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsUnauthorisedExceptionIfStatusIsFourHundredAndOne(): void
    {
        $mock = new MockHandler([
            new Response(401, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Unauthorised::class);
        $this->expectExceptionMessage('API key is invalid.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsForbiddenExceptionIfStatusIsFourHundredAndThree(): void
    {
        $mock = new MockHandler([
            new Response(403, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('No user agent has been specified in the request.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfStatusIsFourHundredAndFour(): void
    {
        $mock = new MockHandler([
            new Response(404, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('The email address could not be found and has therefore not been "pwned".');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfStatusIsFourHundredAndTwentyNine(): void
    {
        $mock = new MockHandler([
            new Response(429, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(TooManyRequests::class);
        $this->expectExceptionMessage('API rate limit has been exceeded.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItPopulatesRetryAfterWhenStatusIsFourHundredAndTwentyNine(): void
    {
        $mock = new MockHandler([
            new Response(429, ['retry-after' => '2'], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        try {
            (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
            $this->fail('Expected TooManyRequests to be thrown.');
        } catch (TooManyRequests $exception) {
            $this->assertSame(2, $exception->getRetryAfter());
        }
    }

    public function testItReturnsNullRetryAfterWhenStatusIsFourHundredAndTwentyNineWithNoRetryAfterHeader(): void
    {
        $mock = new MockHandler([
            new Response(429, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        try {
            (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
            $this->fail('Expected TooManyRequests to be thrown.');
        } catch (TooManyRequests $exception) {
            $this->assertNull($exception->getRetryAfter());
        }
    }

    public function testItThrowsNotFoundExceptionIfStatusIsFiveHundredAndThree(): void
    {
        $mock = new MockHandler([
            new Response(503, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(ServiceUnavailable::class);
        $this->expectExceptionMessage('API unavailable.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItSurfacesHibpResponseMessageWhenStatusIsFourHundred(): void
    {
        $mock = new MockHandler([
            new Response(400, [], '{"message":"The email address is not a valid email format."}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage('The email address is not a valid email format.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItSurfacesHibpResponseMessageWhenStatusIsFourHundredAndOne(): void
    {
        $mock = new MockHandler([
            new Response(401, [], '{"message":"Access denied due to invalid hibp-api-key."}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Unauthorised::class);
        $this->expectExceptionMessage('Access denied due to invalid hibp-api-key.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItSurfacesHibpResponseMessageWhenStatusIsFourHundredAndThree(): void
    {
        $mock = new MockHandler([
            new Response(403, [], '{"message":"Access denied due to insufficient privileges for this resource."}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('Access denied due to insufficient privileges for this resource.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItSurfacesHibpResponseMessageWhenStatusIsFourHundredAndFour(): void
    {
        $mock = new MockHandler([
            new Response(404, [], '{"message":"No results found."}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('No results found.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItSurfacesHibpResponseMessageWhenStatusIsFiveHundredAndThree(): void
    {
        $mock = new MockHandler([
            new Response(503, [], '{"message":"The underlying service is not available."}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(ServiceUnavailable::class);
        $this->expectExceptionMessage('The underlying service is not available.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItFallsBackToDefaultMessageWhenResponseBodyIsNotJson(): void
    {
        $mock = new MockHandler([
            new Response(403, [], 'not json')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('No user agent has been specified in the request.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItFallsBackToDefaultMessageWhenResponseBodyIsEmpty(): void
    {
        $mock = new MockHandler([
            new Response(403, [], '')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('No user agent has been specified in the request.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItFallsBackToDefaultMessageWhenJsonBodyHasNoMessageKey(): void
    {
        $mock = new MockHandler([
            new Response(403, [], '{"statusCode":403}')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('No user agent has been specified in the request.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfStatusIsNotDefinedByHibp(): void
    {
        $mock = new MockHandler([
            new Response(500, [], 'response body')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(UnknownServerError::class);
        $this->expectExceptionMessage('500');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfStatusIsNotDefinedByHibpAndHasNoResponseBody(): void
    {
        $mock = new MockHandler([
            new RequestException('no body', new Request('GET', 'https://example.com'), null)
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(UnknownServerError::class);
        $this->expectExceptionMessage('no body');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfStatusIsNotDefinedByHibpAndHttpClientHasNoResponse(): void
    {
        $mock = new MockHandler([
            new TransferException()
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(UnknownServerError::class);
        $this->expectExceptionMessage('');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsBadRequestExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsFourHundred(): void
    {
        $mock = new MockHandler([
            new Response(400, [], 'response body')
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(BadRequest::class);
        $this->expectExceptionMessage('The provided email address does not comply with an acceptable format.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsUnauthorisedExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsFourHundredAndOne(): void
    {
        $mock = new MockHandler([
            new Response(401, [], 'response body')
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(Unauthorised::class);
        $this->expectExceptionMessage('API key is invalid.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsForbiddenExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsFourHundredAndThree(): void
    {
        $mock = new MockHandler([
            new Response(403, [], 'response body')
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('No user agent has been specified in the request.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsFourHundredAndFour(): void
    {
        $mock = new MockHandler([
            new Response(404, [], 'response body')
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('The email address could not be found and has therefore not been "pwned".');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsFourHundredAndTwentyNine(): void
    {
        $mock = new MockHandler([
            new Response(429, [], 'response body')
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(TooManyRequests::class);
        $this->expectExceptionMessage('API rate limit has been exceeded.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsFiveHundredAndThree(): void
    {
        $mock = new MockHandler([
            new Response(503, [], 'response body')
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(ServiceUnavailable::class);
        $this->expectExceptionMessage('API unavailable.');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsNotDefinedByHibp(): void
    {
        $mock = new MockHandler([
            new Response(500, [], 'response body')
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(UnknownServerError::class);
        $this->expectExceptionMessage('500');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }

    public function testItThrowsNotFoundExceptionIfGuzzleHttpErrorsAreSetToFalseAndStatusIsNotDefinedByHibpAndHttpClientHasNoResponse(): void
    {
        $mock = new MockHandler([
            new TransferException()
        ]);

        $client = new Client(['http_errors' => false, 'handler' => HandlerStack::create($mock)]);

        $this->expectException(UnknownServerError::class);
        $this->expectExceptionMessage('');

        (new ApiCallDummy($client, 'fake-api-key'))->mockCall();
    }
}
