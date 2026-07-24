<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Entity;

use DateTime;
use JsonSerializable;

final class Breach implements JsonSerializable
{
    private string $name;
    private string $title;
    private string $domain;
    private DateTime $breachDate;
    private DateTime $addedDate;
    private DateTime $modifiedDate;
    private int $pwnCount;
    private string $description;
    private array $dataClasses;
    private bool $isVerified;
    private bool $isFabricated;
    private bool $isSensitive;
    private bool $isRetired;
    private bool $isSpamList;
    private bool $isMalware;
    private bool $isSubscriptionFree;
    private bool $isStealerLog;
    private string $logoPath;
    private ?string $attribution = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getBreachDate(): DateTime
    {
        return $this->breachDate;
    }

    public function setBreachDate(DateTime $breachDate): self
    {
        $this->breachDate = $breachDate;

        return $this;
    }

    public function getAddedDate(): DateTime
    {
        return $this->addedDate;
    }

    public function setAddedDate(DateTime $addedDate): self
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    public function getModifiedDate(): DateTime
    {
        return $this->modifiedDate;
    }

    public function setModifiedDate(DateTime $modifiedDate): self
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    public function getPwnCount(): int
    {
        return $this->pwnCount;
    }

    public function setPwnCount(int $pwnCount): self
    {
        $this->pwnCount = $pwnCount;

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

    public function getDataClasses(): array
    {
        return $this->dataClasses;
    }

    public function setDataClasses(array $dataClasses): self
    {
        $this->dataClasses = $dataClasses;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isFabricated(): bool
    {
        return $this->isFabricated;
    }

    public function setIsFabricated(bool $isFabricated): self
    {
        $this->isFabricated = $isFabricated;

        return $this;
    }

    public function isSensitive(): bool
    {
        return $this->isSensitive;
    }

    public function setIsSensitive(bool $isSensitive): self
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    public function isRetired(): bool
    {
        return $this->isRetired;
    }

    public function setIsRetired(bool $isRetired): self
    {
        $this->isRetired = $isRetired;

        return $this;
    }

    public function isSpamList(): bool
    {
        return $this->isSpamList;
    }

    public function setIsSpamList(bool $isSpamList): self
    {
        $this->isSpamList = $isSpamList;

        return $this;
    }

    public function isMalware(): bool
    {
        return $this->isMalware;
    }

    public function setIsMalware(bool $isMalware): self
    {
        $this->isMalware = $isMalware;

        return $this;
    }

    public function isSubscriptionFree(): bool
    {
        return $this->isSubscriptionFree;
    }

    public function setIsSubscriptionFree(bool $isSubscriptionFree): self
    {
        $this->isSubscriptionFree = $isSubscriptionFree;

        return $this;
    }

    public function isStealerLog(): bool
    {
        return $this->isStealerLog;
    }

    public function setIsStealerLog(bool $isStealerLog): self
    {
        $this->isStealerLog = $isStealerLog;

        return $this;
    }

    public function getLogoPath(): string
    {
        return $this->logoPath;
    }

    public function setLogoPath(string $logoPath): self
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    public function getAttribution(): ?string
    {
        return $this->attribution;
    }

    public function setAttribution(?string $attribution): self
    {
        $this->attribution = $attribution;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'Name' => $this->name,
            'Title' => $this->title,
            'Domain' => $this->domain,
            'BreachDate' => $this->breachDate->format('Y-m-d'),
            'AddedDate' => $this->addedDate->format(DateTime::ATOM),
            'ModifiedDate' => $this->modifiedDate->format(DateTime::ATOM),
            'PwnCount' => $this->pwnCount,
            'Description' => $this->description,
            'DataClasses' => $this->dataClasses,
            'IsVerified' => $this->isVerified,
            'IsFabricated' => $this->isFabricated,
            'IsSensitive' => $this->isSensitive,
            'IsRetired' => $this->isRetired,
            'IsSpamList' => $this->isSpamList,
            'IsMalware' => $this->isMalware,
            'IsSubscriptionFree' => $this->isSubscriptionFree,
            'IsStealerLog' => $this->isStealerLog,
            'LogoPath' => $this->logoPath,
            'Attribution' => $this->attribution
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
