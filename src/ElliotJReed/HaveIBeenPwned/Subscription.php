<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned;

use ElliotJReed\HaveIBeenPwned\Builder\SubscriptionStatus as SubscriptionStatusBuilder;
use ElliotJReed\HaveIBeenPwned\Entity\SubscriptionStatus as SubscriptionStatusEntity;

class Subscription extends Api
{
    public function status(): SubscriptionStatusEntity
    {
        $body = $this->queryBreachApi('/subscription/status');

        return SubscriptionStatusBuilder::build(\json_decode($body->read($body->getSize()), true, 512, \JSON_THROW_ON_ERROR));
    }
}
