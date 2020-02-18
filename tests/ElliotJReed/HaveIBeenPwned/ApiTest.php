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
