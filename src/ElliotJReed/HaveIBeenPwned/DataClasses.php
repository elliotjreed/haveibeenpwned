<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

class DataClasses extends Api
{
    public const DATA_CLASS_PASSWORDS = 'Passwords';
    public const DATA_CLASS_AVATARS = 'Avatars';

    public function all(): array
    {
        $body = $this->queryBreachApi('/dataclasses');

        return \json_decode($body->read($body->getSize()), true, 512, \JSON_THROW_ON_ERROR);
    }
}
