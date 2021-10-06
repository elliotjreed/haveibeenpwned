<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

class DataClasses extends Api
{
    public function all(): array
    {
        $body = $this->queryBreachApi('/dataclasses');

        return \json_decode($body->read($body->getSize()), true, 512, \JSON_THROW_ON_ERROR);
    }
}
