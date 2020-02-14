<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

final class Email extends Breach
{
    public function breaches(string $email, bool $unverified = true): array
    {
        $queryString = '?includeUnverified=false';
        if ($unverified) {
            $queryString = '?includeUnverified=true';
        }

        $body = $this->queryApi('/breachedaccount/' . $email . $queryString . '?truncateResponse=false');

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

    public function breachNames(string $email, bool $unverified = true): array
    {
        $queryString = '?includeUnverified=false';
        if ($unverified) {
            $queryString = '?includeUnverified=true';
        }

        $body = $this->queryApi('/breachedaccount/' . $email . $queryString . '?truncateResponse=true');

        $breaches = [];
        $length = $body->getSize();

        if ($length > 0) {
            foreach (\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR) as $breach) {
                $breaches[] = $breach['Name'];
            }

            return $breaches;
        }

        return [];
    }

    public function breachCount(string $email, bool $unverified = true): int
    {
        $queryString = '?includeUnverified=false';
        if ($unverified) {
            $queryString = '?includeUnverified=true';
        }

        $body = $this->queryApi('/breachedaccount/' . $email . $queryString . '?truncateResponse=true');

        $length = $body->getSize();
        if ($length > 0) {
            return \count(\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR));
        }

        return 0;
    }
}
