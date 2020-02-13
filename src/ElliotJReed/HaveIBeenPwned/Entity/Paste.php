<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Entity;

use DateTime;

final class Paste
{
    private string $source;
    private string $id;
    private string $title;
    private DateTime $date;
    private int $emailCount;

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return Paste
     */
    public function setSource(string $source): Paste
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Paste
     */
    public function setId(string $id): Paste
    {
        $this->id = $id;

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
     * @return Paste
     */
    public function setTitle(string $title): Paste
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Paste
     */
    public function setDate(DateTime $date): Paste
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getEmailCount(): int
    {
        return $this->emailCount;
    }

    /**
     * @param int $emailCount
     * @return Paste
     */
    public function setEmailCount(int $emailCount): Paste
    {
        $this->emailCount = $emailCount;

        return $this;
    }
}
