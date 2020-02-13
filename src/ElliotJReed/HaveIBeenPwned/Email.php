<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use GuzzleHttp\ClientInterface;

class Email
{
    private ClientInterface $client;
    private ?string $apiKey = null;

    public function __construct(ClientInterface $client, ?string $apiKey = null)
    {
        $this->client = $client;
        $this->apiKey = $apiKey ?? $_ENV['HAVEIBEENPWNED_API_KEY'];
    }

    public function breaches(string $email): array
    {
        if ($this->apiKey === null) {
            // throw no api key exception
        }
        $response = $this->client->request('GET', 'https://haveibeenpwned.com/api/v3/breachedaccount/' . $email, ['headers' => [
            'hibp-api-key' => $this->apiKey,
            'user-agent'
        ]]);

        if ($response->getStatusCode() !== 200) {
            // throw hibp unavailable except
        }

        $body = $response->getBody();

        $breaches = \json_decode($body->read($body->getSize()), true, 512, JSON_THROW_ON_ERROR);

        return $breaches;
    }
}
