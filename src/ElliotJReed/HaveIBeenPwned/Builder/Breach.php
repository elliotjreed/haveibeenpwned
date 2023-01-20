<?php

declare(strict_types=1);

namespace ElliotJReed\HaveIBeenPwned\Builder;

use ElliotJReed\HaveIBeenPwned\Entity\Breach as BreachEntity;

final class Breach
{
    public static function build(array $breach): BreachEntity
    {
        return (new BreachEntity())
            ->setName($breach['Name'])
            ->setTitle($breach['Title'])
            ->setDomain($breach['Domain'])
            ->setBreachDate((new \DateTime($breach['BreachDate']))->setTime(0, 0, 0, 0))
            ->setAddedDate(new \DateTime($breach['AddedDate']))
            ->setModifiedDate(new \DateTime($breach['ModifiedDate']))
            ->setPwnCount($breach['PwnCount'])
            ->setDescription($breach['Description'])
            ->setDataClasses($breach['DataClasses'])
            ->setIsVerified($breach['IsVerified'])
            ->setIsFabricated($breach['IsFabricated'])
            ->setIsSensitive($breach['IsSensitive'])
            ->setIsRetired($breach['IsRetired'])
            ->setIsSpamList($breach['IsSpamList'])
            ->setLogoPath($breach['LogoPath']);
    }
}
