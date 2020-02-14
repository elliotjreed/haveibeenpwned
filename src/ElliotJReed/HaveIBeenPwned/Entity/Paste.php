<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Entity;

use DateTime;

final class Paste
{
    private string $source;
    private string $id;
    private ?string $title = null;
    private DateTime $date;
    private int $emailCount;

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): Paste
    {
        $this->source = $source;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): Paste
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Paste
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): Paste
    {
        $this->date = $date;

        return $this;
    }

    public function getEmailCount(): int
    {
        return $this->emailCount;
    }

    public function setEmailCount(int $emailCount): Paste
    {
        $this->emailCount = $emailCount;

        return $this;
    }
}
