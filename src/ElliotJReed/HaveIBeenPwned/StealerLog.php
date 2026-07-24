<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Exception\NotFound;

class StealerLog extends Api
{
    public function byEmail(string $email): array
    {
        return $this->fetch('/stealerlogsbyemail/' . $this->encodeUrl($email));
    }

    public function byWebsiteDomain(string $domain): array
    {
        return $this->fetch('/stealerlogsbywebsitedomain/' . $this->encodeUrl($domain));
    }

    public function byEmailDomain(string $domain): array
    {
        return $this->fetch('/stealerlogsbyemaildomain/' . $this->encodeUrl($domain));
    }

    private function fetch(string $endPoint): array
    {
        $result = [];
        try {
            $body = $this->queryBreachApi($endPoint);

            $length = $body->getSize();
            if ($length > 0) {
                $result = \json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR);
            }
        } catch (NotFound $exception) {
        }

        return $result;
    }
}
