<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Entity;

use DateTime;

final class Breach
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
    private string $logoPath;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Breach
     */
    public function setName(string $name): Breach
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Breach
     */
    public function setTitle(string $title): Breach
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Breach
     */
    public function setDomain(string $domain): Breach
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBreachDate(): DateTime
    {
        return $this->breachDate;
    }

    /**
     * @param DateTime $breachDate
     * @return Breach
     */
    public function setBreachDate(DateTime $breachDate): Breach
    {
        $this->breachDate = $breachDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getAddedDate(): DateTime
    {
        return $this->addedDate;
    }

    /**
     * @param DateTime $addedDate
     * @return Breach
     */
    public function setAddedDate(DateTime $addedDate): Breach
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getModifiedDate(): DateTime
    {
        return $this->modifiedDate;
    }

    /**
     * @param DateTime $modifiedDate
     * @return Breach
     */
    public function setModifiedDate(DateTime $modifiedDate): Breach
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getPwnCount(): int
    {
        return $this->pwnCount;
    }

    /**
     * @param int $pwnCount
     * @return Breach
     */
    public function setPwnCount(int $pwnCount): Breach
    {
        $this->pwnCount = $pwnCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Breach
     */
    public function setDescription(string $description): Breach
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getDataClasses(): array
    {
        return $this->dataClasses;
    }

    /**
     * @param array $dataClasses
     * @return Breach
     */
    public function setDataClasses(array $dataClasses): Breach
    {
        $this->dataClasses = $dataClasses;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     * @return Breach
     */
    public function setIsVerified(bool $isVerified): Breach
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFabricated(): bool
    {
        return $this->isFabricated;
    }

    /**
     * @param bool $isFabricated
     * @return Breach
     */
    public function setIsFabricated(bool $isFabricated): Breach
    {
        $this->isFabricated = $isFabricated;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSensitive(): bool
    {
        return $this->isSensitive;
    }

    /**
     * @param bool $isSensitive
     * @return Breach
     */
    public function setIsSensitive(bool $isSensitive): Breach
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRetired(): bool
    {
        return $this->isRetired;
    }

    /**
     * @param bool $isRetired
     * @return Breach
     */
    public function setIsRetired(bool $isRetired): Breach
    {
        $this->isRetired = $isRetired;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSpamList(): bool
    {
        return $this->isSpamList;
    }

    /**
     * @param bool $isSpamList
     * @return Breach
     */
    public function setIsSpamList(bool $isSpamList): Breach
    {
        $this->isSpamList = $isSpamList;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogoPath(): string
    {
        return $this->logoPath;
    }

    /**
     * @param string $logoPath
     * @return Breach
     */
    public function setLogoPath(string $logoPath): Breach
    {
        $this->logoPath = $logoPath;

        return $this;
    }
}
