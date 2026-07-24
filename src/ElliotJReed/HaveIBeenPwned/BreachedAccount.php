<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Builder\Breach;
use ElliotJReed\HaveIBeenPwned\Exception\NotFound;

class BreachedAccount extends Api
{
    public function breaches(string $account, bool $unverified = true, ?string $domain = null): array
    {
        $breaches = [];
        try {
            $body = $this->queryBreachApi('/breachedaccount/' . $this->encodeUrl($account) . $this->buildQueryString(false, $unverified, $domain));

            $length = $body->getSize();
            if ($length > 0) {
                foreach (\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR) as $breach) {
                    $breaches[] = Breach::build($breach);
                }
            }
        } catch (NotFound $exception) {
        }

        return $breaches;
    }

    public function breachNames(string $account, bool $unverified = true, ?string $domain = null): array
    {
        $breaches = [];
        try {
            $body = $this->queryBreachApi('/breachedaccount/' . $this->encodeUrl($account) . $this->buildQueryString(true, $unverified, $domain));

            $length = $body->getSize();
            if ($length > 0) {
                foreach (\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR) as $breach) {
                    $breaches[] = $breach['Name'];
                }
            }
        } catch (NotFound $exception) {
        }

        return $breaches;
    }

    public function count(string $email, bool $unverified = true, ?string $domain = null): int
    {
        $count = 0;
        try {
            $body = $this->queryBreachApi('/breachedaccount/' . $this->encodeUrl($email) . $this->buildQueryString(true, $unverified, $domain));

            $length = $body->getSize();
            if ($length > 0) {
                $count = \count(\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR));
            }
        } catch (NotFound $exception) {
        }

        return $count;
    }

    public function isBreached(string $email): bool
    {
        return $this->count($email) > 0;
    }

    public function breachNamesByHashRange(string $account): array
    {
        $hash = \strtoupper(\sha1(\strtolower(\trim($account))));
        $prefix = \substr($hash, 0, 6);
        $suffix = \substr($hash, 6);

        $body = $this->queryBreachApi('/breachedaccount/range/' . $prefix);

        $length = $body->getSize();
        if ($length > 0) {
            foreach (\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR) as $entry) {
                if ($entry['hashSuffix'] === $suffix) {
                    return $entry['websites'];
                }
            }
        }

        return [];
    }

    private function buildQueryString(bool $truncateResponse, bool $unverified, ?string $domain): string
    {
        $queryString = '?truncateResponse=' . ($truncateResponse ? 'true' : 'false');

        if (!$unverified) {
            $queryString .= '&includeUnverified=false';
        }

        if (null !== $domain) {
            $queryString .= '&domain=' . $this->encodeUrl($domain);
        }

        return $queryString;
    }
}
