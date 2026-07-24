<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Builder;

use DateTime;
use ElliotJReed\HaveIBeenPwned\Entity\SubscribedDomain as SubscribedDomainEntity;

final class SubscribedDomain
{
    public static function build(array $domain): SubscribedDomainEntity
    {
        return (new SubscribedDomainEntity())
            ->setDomainName($domain['DomainName'])
            ->setPwnCount($domain['PwnCount'] ?? null)
            ->setPwnCountExcludingSpamLists($domain['PwnCountExcludingSpamLists'] ?? null)
            ->setPwnCountExcludingSpamListsAtLastSubscriptionRenewal($domain['PwnCountExcludingSpamListsAtLastSubscriptionRenewal'] ?? null)
            ->setNextSubscriptionRenewal(isset($domain['NextSubscriptionRenewal']) ? new DateTime($domain['NextSubscriptionRenewal']) : null);
    }
}
