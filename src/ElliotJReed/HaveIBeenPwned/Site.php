<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Entity\Breach as BreachEntity;

class Site extends Breach
{
    public function all(): array
    {
        $body = $this->queryApi('/breaches');

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

    public function byDomain(string $domain): array
    {
        $body = $this->queryApi('/breaches?domain=' . \trim($domain));

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

    public function byName(string $siteName): ?BreachEntity
    {
        $body = $this->queryApi('/breach/' . $siteName);
        $length = $body->getSize();
        if ($length > 0) {
            return $this->buildBreach(\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR));
        }

        return null;
    }
}
