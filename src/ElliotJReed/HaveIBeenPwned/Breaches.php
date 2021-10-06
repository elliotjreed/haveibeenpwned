<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Builder\Breach;
use ElliotJReed\HaveIBeenPwned\Entity\Breach as BreachEntity;
use ElliotJReed\HaveIBeenPwned\Exception\NotFound;

class Breaches extends Api
{
    public function allSources(): array
    {
        $breaches = [];
        try {
            $body = $this->queryBreachApi('/breaches');
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

    public function byDomain(string $domain): array
    {
        $breaches = [];
        try {
            $body = $this->queryBreachApi('/breaches?domain=' . $this->encodeUrl($domain));

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

    public function bySourceName(string $siteName): ?BreachEntity
    {
        try {
            $body = $this->queryBreachApi('/breach/' . $this->encodeUrl($siteName));

            $length = $body->getSize();
            if ($length > 0) {
                return Breach::build(\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR));
            }
        } catch (NotFound $exception) {
        }

        return null;
    }
}
