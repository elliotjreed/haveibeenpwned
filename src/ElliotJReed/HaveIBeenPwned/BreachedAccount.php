<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Builder\Breach;
use ElliotJReed\HaveIBeenPwned\Exception\NotFound;

class BreachedAccount extends Api
{
    public function breaches(string $account, bool $unverified = true): array
    {
        $queryString = '?truncateResponse=false';
        if (!$unverified) {
            $queryString .= '&?includeUnverified=false';
        }

        $breaches = [];
        try {
            $body = $this->queryBreachApi('/breachedaccount/' . $this->encodeUrl($account) . $queryString);

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

    public function breachNames(string $account, bool $unverified = true): array
    {
        $queryString = '?truncateResponse=true';
        if (!$unverified) {
            $queryString .= '&?includeUnverified=false';
        }

        $breaches = [];
        try {
            $body = $this->queryBreachApi('/breachedaccount/' . $this->encodeUrl($account) . $queryString);

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

    public function count(string $email, bool $unverified = true): int
    {
        $queryString = '?truncateResponse=true';
        if (!$unverified) {
            $queryString .= '&?includeUnverified=false';
        }

        $count = 0;
        try {
            $body = $this->queryBreachApi('/breachedaccount/' . $this->encodeUrl($email) . $queryString);

            $length = $body->getSize();
            if ($length > 0) {
                $count = \count(\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR));
            }
        } catch (NotFound $exception) {
        }

        return $count;
    }

    /**
     * @return \DateTimeInterface|null a \DateTimeInterface or null if the given dataClass did not match
     */
    public function lastDataClassBreachDate(string $account, string $dataClass, bool $unverified = true): ?\DateTimeInterface
    {
        return array_reduce(
            $this->breaches($account, $unverified),
            function (?\DateTimeInterface $carry, \ElliotJReed\HaveIBeenPwned\Entity\Breach $breach) use ($dataClass): ?\DateTimeInterface {
                if (!\in_array($dataClass, $breach->getDataClasses())) {
                    return $carry;
                }

                if ($breach->getBreachDate() > $carry) {
                    return $breach->getBreachDate();
                }

                return $carry;
            },
            null
        );
    }

    public function isDataClassBreached(string $account, string $dataClass, bool $unverified = true): bool
    {
        return $this->lastDataClassBreachDate($account, $dataClass, $unverified) !== null;
    }
}
