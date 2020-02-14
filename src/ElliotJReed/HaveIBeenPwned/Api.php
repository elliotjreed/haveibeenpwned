<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

abstract class Api
{
    private const HIBP_BASE_URI = 'https://haveibeenpwned.com/api/v3';
    private ClientInterface $client;
    private string $apiKey;

    public function __construct(ClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    protected function queryApi(string $endPoint): StreamInterface
    {
        try {
            $response = $this->client->request('GET', self::HIBP_BASE_URI . $endPoint, ['headers' => [
                'hibp-api-key' => $this->apiKey,
                'user-agent' => 'www.elliotjreed.com'
            ]]);
            if ($response->getStatusCode() !== 200) {
                // throw hibp unavailable except
            }
        } catch (GuzzleException $exception) {
            // throw hibp unavailable except
        }

        return $response->getBody();
    }
}
