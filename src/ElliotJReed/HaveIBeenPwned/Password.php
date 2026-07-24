<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

class Password extends Api
{
    private const HIBP_BASE_URI = 'https://api.pwnedpasswords.com';

    public function count(string $password, bool $ntlm = false, bool $addPadding = false): int
    {
        $hashedPassword = $ntlm
            ? \strtoupper(\hash('md4', \mb_convert_encoding($password, 'UTF-16LE', 'UTF-8')))
            : \strtoupper(\sha1($password));
        $firstFiveCharacters = \substr($hashedPassword, 0, 5);

        $endPoint = '/range/' . $firstFiveCharacters . ($ntlm ? '?mode=ntlm' : '');
        $headers = $addPadding ? ['Add-Padding' => 'true'] : [];

        $body = $this->queryBreachApi($endPoint, self::HIBP_BASE_URI, $headers);
        $hashes = \str_replace("\r\n", \PHP_EOL, $body->read($body->getSize()));

        foreach (\explode(\PHP_EOL, $hashes) as $line) {
            if (\str_contains($line, ':')) {
                [$hash, $count] = \explode(':', $line);
                if ($firstFiveCharacters . \strtoupper($hash) === $hashedPassword) {
                    return (int) $count;
                }
            }
        }

        return 0;
    }

    public function isPwned(string $password, bool $ntlm = false, bool $addPadding = false): bool
    {
        return $this->count($password, $ntlm, $addPadding) > 0;
    }
}
