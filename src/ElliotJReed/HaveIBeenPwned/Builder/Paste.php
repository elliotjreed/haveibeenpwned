<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Builder;

use DateTime;
use ElliotJReed\HaveIBeenPwned\Entity\Paste as PasteEntity;

final class Paste
{
    public static function build(array $paste): PasteEntity
    {
        return (new PasteEntity())
            ->setSource($paste['Source'])
            ->setId($paste['Id'])
            ->setTitle($paste['Title'] ?? null)
            ->setDate(new DateTime($paste['Date']))
            ->setEmailCount($paste['EmailCount']);
    }
}
