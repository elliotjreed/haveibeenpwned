<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use DateTime;
use ElliotJReed\HaveIBeenPwned\Entity\Paste;

final class Pastes extends Api
{
    public function all(string $email): array
    {
        $body = $this->queryApi('/pasteaccount/' . $email);

        $breaches = [];
        $length = $body->getSize();

        if ($length > 0) {
            foreach (\json_decode($body->read($length), true, 512, JSON_THROW_ON_ERROR) as $breach) {
                $breaches[] = $this->buildPaste($breach);
            }

            return $breaches;
        }

        return [];
    }

    private function buildPaste(array $breach): Paste
    {
        return (new Paste())
            ->setSource($breach['Source'])
            ->setId($breach['Id'])
            ->setTitle($breach['Title'] ?? null)
            ->setDate(new DateTime($breach['Date']))
            ->setEmailCount($breach['EmailCount']);
    }
}
