<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Entity;

use DateTime;

final class SubscribedDomain
{
    private string $domainName;
    private ?int $pwnCount = null;
    private ?int $pwnCountExcludingSpamLists = null;
    private ?int $pwnCountExcludingSpamListsAtLastSubscriptionRenewal = null;
    private ?DateTime $nextSubscriptionRenewal = null;

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function setDomainName(string $domainName): self
    {
        $this->domainName = $domainName;

        return $this;
    }

    public function getPwnCount(): ?int
    {
        return $this->pwnCount;
    }

    public function setPwnCount(?int $pwnCount): self
    {
        $this->pwnCount = $pwnCount;

        return $this;
    }

    public function getPwnCountExcludingSpamLists(): ?int
    {
        return $this->pwnCountExcludingSpamLists;
    }

    public function setPwnCountExcludingSpamLists(?int $pwnCountExcludingSpamLists): self
    {
        $this->pwnCountExcludingSpamLists = $pwnCountExcludingSpamLists;

        return $this;
    }

    public function getPwnCountExcludingSpamListsAtLastSubscriptionRenewal(): ?int
    {
        return $this->pwnCountExcludingSpamListsAtLastSubscriptionRenewal;
    }

    public function setPwnCountExcludingSpamListsAtLastSubscriptionRenewal(?int $pwnCountExcludingSpamListsAtLastSubscriptionRenewal): self
    {
        $this->pwnCountExcludingSpamListsAtLastSubscriptionRenewal = $pwnCountExcludingSpamListsAtLastSubscriptionRenewal;

        return $this;
    }

    public function getNextSubscriptionRenewal(): ?DateTime
    {
        return $this->nextSubscriptionRenewal;
    }

    public function setNextSubscriptionRenewal(?DateTime $nextSubscriptionRenewal): self
    {
        $this->nextSubscriptionRenewal = $nextSubscriptionRenewal;

        return $this;
    }
}
