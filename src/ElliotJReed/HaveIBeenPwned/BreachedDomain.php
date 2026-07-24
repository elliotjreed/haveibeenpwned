<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Exception\NotFound;

class BreachedDomain extends Api
{
    public function search(string $domain): array
    {
        $breaches = [];
        try {
            $body = $this->queryBreachApi('/breacheddomain/' . $this->encodeUrl($domain));

            $length = $body->getSize();
            if ($length > 0) {
                $breaches = \json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR);
            }
        } catch (NotFound $exception) {
        }

        return $breaches;
    }
}
