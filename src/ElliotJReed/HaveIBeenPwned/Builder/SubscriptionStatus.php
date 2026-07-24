<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Builder;

use DateTime;
use ElliotJReed\HaveIBeenPwned\Entity\SubscriptionStatus as SubscriptionStatusEntity;

final class SubscriptionStatus
{
    public static function build(array $status): SubscriptionStatusEntity
    {
        return (new SubscriptionStatusEntity())
            ->setSubscriptionName($status['SubscriptionName'])
            ->setDescription($status['Description'])
            ->setSubscribedUntil(new DateTime($status['SubscribedUntil']))
            ->setRpm($status['Rpm'])
            ->setDomainSearchMaxBreachedAccounts($status['DomainSearchMaxBreachedAccounts'])
            ->setMaxBreachedDomains($status['MaxBreachedDomains'] ?? null)
            ->setIncludesStealerLogs($status['IncludesStealerLogs'])
            ->setIncludesBulkDomainAdd($status['IncludesBulkDomainAdd'])
            ->setIncludesAutoSubdomainVerification($status['IncludesAutoSubdomainVerification'])
            ->setIncludesCustomerDomains($status['IncludesCustomerDomains'])
            ->setIncludesKAnon($status['IncludesKAnon']);
    }
}
