<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

class DomainVerification extends Api
{
    public function generateDnsToken(string $domain): string
    {
        $body = $this->postToBreachApi('/domainverification/generatednstoken', ['DomainName' => $domain]);

        return \json_decode($body->read($body->getSize()), true, 512, \JSON_THROW_ON_ERROR)['txtRecordValue'];
    }

    public function verifyDnsToken(string $domain): ?string
    {
        $body = $this->postToBreachApi('/domainverification/verifydnstoken', ['DomainName' => $domain]);

        $length = $body->getSize();
        if ($length > 0) {
            return \json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR)['message'] ?? null;
        }

        return null;
    }

    public function sendEmail(string $domain, string $alias): void
    {
        $this->postToBreachApi('/domainverification/sendemail', ['DomainName' => $domain, 'EmailAlias' => $alias]);
    }
}
