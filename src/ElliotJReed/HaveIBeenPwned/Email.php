<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use DateTime;
use ElliotJReed\HaveIBeenPwned\Entity\Breach;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

class Email
{
    private ClientInterface $client;
    private string $apiKey;

    public function __construct(ClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $email
     * @param bool $truncate
     * @param bool $unverified
     * @param string|null $domain
     * @return array
     */
    public function breaches(string $email, bool $truncate = false, bool $unverified = true, ?string $domain = null): array
    {
        $queryString = $this->buildBreachesQueryString($truncate, $unverified, $domain);

        $body = $this->queryApi('https://haveibeenpwned.com/api/v3/breachedaccount/' . $email . $queryString);

        $breaches = [];
        $length = $body->getSize();

        if ($length > 0) {
            foreach (\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR) as $breach) {
                $breaches[] = $this->buildBreach($breach);
            }

            return $breaches;
        }

        return [];
    }

    /**
     * @param string|null $domain
     * @return array
     */
    public function allBreaches(?string $domain = null): array
    {
        $domainParameter = '';
        if ($domain !== null) {
            $domainParameter = '?domain=' . $domain;
        }

        $body = $this->queryApi('https://haveibeenpwned.com/api/v3/breaches' . $domainParameter);

        $breaches = [];
        $length = $body->getSize();

        if ($length > 0) {
            foreach (\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR) as $breach) {
                $breaches[] = $this->buildBreach($breach);
            }

            return $breaches;
        }

        return [];
    }

    /**
     * @param string $siteName
     * @return Breach|null
     */
    public function breachedSite(string $siteName): ?Breach
    {
        $body = $this->queryApi('https://haveibeenpwned.com/api/v3/breach/' . $siteName);
        $length = $body->getSize();
        if ($length > 0) {
            return $this->buildBreach(\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR));
        }

        return null;
    }

    /**
     * @return array
     */
    public function dataClasses(): array
    {
        $body = $this->queryApi('https://haveibeenpwned.com/api/v3/dataclasses');

        return \json_decode($body->read($body->getSize()), true, 512, JSON_THROW_ON_ERROR);
    }

    public function pastes(string $email): array
    {
        $body = $this->queryApi('https://haveibeenpwned.com/api/v3/pasteaccount/' . $email);

        $breaches = [];
        $length = $body->getSize();

        if ($length > 0) {
            foreach (\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR) as $breach) {
                $breaches[] = $this->buildBreach($breach);
            }

            return $breaches;
        }

        return [];
    }

    /**
     * @param bool $truncate
     * @param bool $unverified
     * @param string|null $domain
     * @return string
     */
    private function buildBreachesQueryString(bool $truncate, bool $unverified, ?string $domain): string
    {
        $queryString = '';
        if (!$truncate) {
            $queryString .= '?truncateResponse=false';
        }

        if ($unverified) {
            $queryString .= '?includeUnverified=false';
        }

        if ($domain !== null) {
            $queryString .= '?domain=' . $domain;
        }

        return $queryString;
    }

    /**
     * @param $breach
     * @return Breach
     */
    private function buildBreach(array $breach): Breach
    {
        return (new Breach())
            ->setName($breach['Name'])
            ->setTitle($breach['Title'])
            ->setDomain($breach['Domain'])
            ->setBreachDate(new DateTime($breach['BreachDate']))
            ->setAddedDate(new DateTime($breach['AddedDate']))
            ->setModifiedDate(new DateTime($breach['ModifiedDate']))
            ->setPwnCount($breach['PwnCount'])
            ->setDescription($breach['Description'])
            ->setDataClasses($breach['DataClasses'])
            ->setIsVerified($breach['IsVerified'])
            ->setIsFabricated($breach['IsFabricated'])
            ->setIsSensitive($breach['IsSensitive'])
            ->setIsRetired($breach['IsRetired'])
            ->setIsSpamList($breach['IsSpamList'])
            ->setLogoPath($breach['LogoPath']);
    }

    /**
     * @param string $queryString
     * @return StreamInterface
     */
    private function queryApi(string $queryString): StreamInterface
    {
        try {
            $response = $this->client->request('GET', $queryString, ['headers' => [
                'hibp-api-key' => $this->apiKey,
                'user-agent'
            ]]);
            if ($response->getStatusCode() !== 200) {
                // throw hibp unavailable except
            }

            return $response->getBody();
        } catch (GuzzleException $exception) {
            // throw hibp unavailable except
        }
    }
}
