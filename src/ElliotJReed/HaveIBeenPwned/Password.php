<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

class Password extends Api
{
    private const HIBP_BASE_URI = 'https://api.pwnedpasswords.com';

    public function byPassword(string $password): int
    {
        $hashedPassword = \strtoupper(sha1($password));
        $firstFiveCharacters = \substr($hashedPassword, 0, 5);
        $body = $this->queryBreachApi('/range/' . $firstFiveCharacters, self::HIBP_BASE_URI);
        $hashes = \str_replace("\r\n", \PHP_EOL, $body->read($body->getSize()));

        foreach (\explode(\PHP_EOL, $hashes) as $line) {
            [$hash, $count] = \explode(':', $line);
            if ($firstFiveCharacters . \strtoupper($hash) === $hashedPassword) {
                return (int) $count;
            }
        }

        return 0;
    }
}
