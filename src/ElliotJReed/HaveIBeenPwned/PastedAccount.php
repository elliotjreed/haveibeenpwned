<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Builder\Paste as PasteBuilder;
use ElliotJReed\HaveIBeenPwned\Exception\NotFound;

class PastedAccount extends Api
{
    public function pastes(string $account): array
    {
        $breaches = [];
        try {
            $body = $this->queryBreachApi('/pasteaccount/' . $this->encodeUrl($account));

            $length = $body->getSize();
            if ($length > 0) {
                foreach (\json_decode($body->read($length), true, 512, \JSON_THROW_ON_ERROR) as $breach) {
                    $breaches[] = PasteBuilder::build($breach);
                }
            }
        } catch (NotFound $exception) {
        }

        return $breaches;
    }
}
