<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Entity;

use DateTime;

final class SubscriptionStatus
{
    private string $subscriptionName;
    private string $description;
    private DateTime $subscribedUntil;
    private int $rpm;
    private int $domainSearchMaxBreachedAccounts;
    private ?int $maxBreachedDomains = null;
    private bool $includesStealerLogs;
    private bool $includesBulkDomainAdd;
    private bool $includesAutoSubdomainVerification;
    private bool $includesCustomerDomains;
    private bool $includesKAnon;

    public function getSubscriptionName(): string
    {
        return $this->subscriptionName;
    }

    public function setSubscriptionName(string $subscriptionName): self
    {
        $this->subscriptionName = $subscriptionName;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSubscribedUntil(): DateTime
    {
        return $this->subscribedUntil;
    }

    public function setSubscribedUntil(DateTime $subscribedUntil): self
    {
        $this->subscribedUntil = $subscribedUntil;

        return $this;
    }

    public function getRpm(): int
    {
        return $this->rpm;
    }

    public function setRpm(int $rpm): self
    {
        $this->rpm = $rpm;

        return $this;
    }

    public function getDomainSearchMaxBreachedAccounts(): int
    {
        return $this->domainSearchMaxBreachedAccounts;
    }

    public function setDomainSearchMaxBreachedAccounts(int $domainSearchMaxBreachedAccounts): self
    {
        $this->domainSearchMaxBreachedAccounts = $domainSearchMaxBreachedAccounts;

        return $this;
    }

    public function getMaxBreachedDomains(): ?int
    {
        return $this->maxBreachedDomains;
    }

    public function setMaxBreachedDomains(?int $maxBreachedDomains): self
    {
        $this->maxBreachedDomains = $maxBreachedDomains;

        return $this;
    }

    public function includesStealerLogs(): bool
    {
        return $this->includesStealerLogs;
    }

    public function setIncludesStealerLogs(bool $includesStealerLogs): self
    {
        $this->includesStealerLogs = $includesStealerLogs;

        return $this;
    }

    public function includesBulkDomainAdd(): bool
    {
        return $this->includesBulkDomainAdd;
    }

    public function setIncludesBulkDomainAdd(bool $includesBulkDomainAdd): self
    {
        $this->includesBulkDomainAdd = $includesBulkDomainAdd;

        return $this;
    }

    public function includesAutoSubdomainVerification(): bool
    {
        return $this->includesAutoSubdomainVerification;
    }

    public function setIncludesAutoSubdomainVerification(bool $includesAutoSubdomainVerification): self
    {
        $this->includesAutoSubdomainVerification = $includesAutoSubdomainVerification;

        return $this;
    }

    public function includesCustomerDomains(): bool
    {
        return $this->includesCustomerDomains;
    }

    public function setIncludesCustomerDomains(bool $includesCustomerDomains): self
    {
        $this->includesCustomerDomains = $includesCustomerDomains;

        return $this;
    }

    public function includesKAnon(): bool
    {
        return $this->includesKAnon;
    }

    public function setIncludesKAnon(bool $includesKAnon): self
    {
        $this->includesKAnon = $includesKAnon;

        return $this;
    }
}
