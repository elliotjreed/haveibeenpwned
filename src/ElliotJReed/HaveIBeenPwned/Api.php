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

    protected function queryBreachApi(string $endPoint, string $baseUri = self::HIBP_BASE_URI): StreamInterface
    {
        try {
            $response = $this->sendRequest($baseUri . $endPoint)->getBody();
        } catch (RequestException $exception) {
            $this->handleRequestException($exception);
        } catch (GuzzleException $exception) {
            throw new UnknownServerError($exception->getMessage(), (int) $exception->getCode(), $exception->getPrevious());
        }

        return $response;
    }

    protected function encodeUrl(string $input): string
    {
        return \rawurlencode(\strtolower(\trim($input)));
    }

    private function sendRequest(string $endPoint): ResponseInterface
    {
        $response = $this->client->request('GET', $endPoint, ['headers' => [
            'hibp-api-key' => $this->apiKey,
            'user-agent' => 'hibp-php'
        ]]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            $this->handleNotOkResponse($statusCode);
        }

        return $response;
    }

    private function handleNotOkResponse(int $statusCode): void
    {
        switch ($statusCode) {
            case 400:
                throw new BadRequest();
            case 401:
                throw new Unauthorised();
            case 403:
                throw new Forbidden();
            case 404:
                throw new NotFound();
            case 429:
                throw new TooManyRequests();
            case 503:
                throw new ServiceUnavailable();
            default:
                throw new UnknownServerError((string) $statusCode);
        }
    }

    protected function handleRequestException(RequestException $exception): void
    {
        if ($exception->hasResponse()) {
            $statusCode = $exception->getResponse()->getStatusCode();
            $this->handleNotOkResponse($statusCode);
        }

        throw new UnknownServerError($exception->getMessage(), (int) $exception->getCode(), $exception->getPrevious());
    }
}
