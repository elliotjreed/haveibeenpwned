<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Exception\BadRequest;
use ElliotJReed\HaveIBeenPwned\Exception\Forbidden;
use ElliotJReed\HaveIBeenPwned\Exception\NotFound;
use ElliotJReed\HaveIBeenPwned\Exception\ServiceUnavailable;
use ElliotJReed\HaveIBeenPwned\Exception\TooManyRequests;
use ElliotJReed\HaveIBeenPwned\Exception\Unauthorised;
use ElliotJReed\HaveIBeenPwned\Exception\UnknownServerError;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class Api
{
    private const HIBP_BASE_URI = 'https://haveibeenpwned.com/api/v3';
    private ClientInterface $client;
    private string $apiKey;

    public function __construct(ClientInterface $client, string $apiKey = '')
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    protected function queryBreachApi(string $endPoint, string $baseUri = self::HIBP_BASE_URI, array $headers = []): StreamInterface
    {
        return $this->call('GET', $baseUri . $endPoint, $headers);
    }

    protected function postToBreachApi(string $endPoint, array $body, string $baseUri = self::HIBP_BASE_URI): StreamInterface
    {
        return $this->call('POST', $baseUri . $endPoint, [], $body);
    }

    protected function encodeUrl(string $input): string
    {
        return \rawurlencode(\strtolower(\trim($input)));
    }

    private function call(string $method, string $endPoint, array $headers, ?array $body = null): StreamInterface
    {
        try {
            $response = $this->sendRequest($method, $endPoint, $headers, $body)->getBody();
        } catch (RequestException $exception) {
            $this->handleRequestException($exception);
        } catch (GuzzleException $exception) {
            throw new UnknownServerError($exception->getMessage(), (int) $exception->getCode(), $exception->getPrevious());
        }

        return $response;
    }

    private function sendRequest(string $method, string $endPoint, array $headers, ?array $body): ResponseInterface
    {
        $options = [
            'headers' => \array_merge([
                'hibp-api-key' => $this->apiKey,
                'user-agent' => 'hibp-php'
            ], $headers)
        ];

        if (null !== $body) {
            $options['json'] = $body;
        }

        $response = $this->client->request($method, $endPoint, $options);
        $statusCode = $response->getStatusCode();
        if (200 !== $statusCode) {
            $this->handleNotOkResponse($response);
        }

        return $response;
    }

    private function handleNotOkResponse(ResponseInterface $response): never
    {
        $detail = $this->parseErrorDetail($response);

        switch ($response->getStatusCode()) {
            case 400:
                throw new BadRequest($detail);
            case 401:
                throw new Unauthorised($detail);
            case 403:
                throw new Forbidden($detail);
            case 404:
                throw new NotFound($detail);
            case 429:
                throw new TooManyRequests($this->parseRetryAfter($response));
            case 503:
                throw new ServiceUnavailable($detail);
            default:
                throw new UnknownServerError((string) $response->getStatusCode());
        }
    }

    private function parseErrorDetail(ResponseInterface $response): ?string
    {
        $decoded = \json_decode((string) $response->getBody(), true);

        return \is_array($decoded) && \is_string($decoded['message'] ?? null) ? $decoded['message'] : null;
    }

    private function parseRetryAfter(ResponseInterface $response): ?int
    {
        $retryAfter = $response->getHeaderLine('retry-after');

        return '' === $retryAfter ? null : (int) $retryAfter;
    }

    protected function handleRequestException(RequestException $exception): never
    {
        if ($exception->hasResponse()) {
            $this->handleNotOkResponse($exception->getResponse());
        }

        throw new UnknownServerError($exception->getMessage(), (int) $exception->getCode(), $exception->getPrevious());
    }
}
