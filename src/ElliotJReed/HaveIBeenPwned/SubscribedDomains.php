<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Builder\SubscribedDomain;

class SubscribedDomains extends Api
{
    public function all(): array
    {
        $domains = [];
        $body = $this->queryBreachApi('/subscribeddomains');

        $length = $body->getSize();
        if ($length > 0) {
            foreach (\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR) as $domain) {
                $domains[] = SubscribedDomain::build($domain);
            }
        }

        return $domains;
    }
}
