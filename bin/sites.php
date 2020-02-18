#!/usr/bin/env php
<?php

declare(strict_types=1);

use ElliotJReed\HaveIBeenPwned\Breaches;
use GuzzleHttp\Client;

if (\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) === false) {
    echo 'Error: This command should be invoked via the CLI version of PHP, not the ' . \PHP_SAPI . ' SAPI' . \PHP_EOL;
    exit(1);
}

if ($argc > 1) {
    echo "Warning: This application requires no additional argument (e.g. ./sites.php). Arguments will be ignored." . \PHP_EOL;
}

require __DIR__ . '/../vendor/autoload.php';

$guzzle = new Client();
$apiKey = 'YOUR_API_KEY';

foreach ((new Breaches($guzzle, $apiKey))->all() as $breach) {
    echo 'Name: ' . $breach->getName() . PHP_EOL;
    echo 'Title: ' . $breach->getTitle() . PHP_EOL;
    echo 'Domain: ' . $breach->getDomain() . PHP_EOL;
    echo 'Breach Date: ' . $breach->getBreachDate()->format('d/m/Y g:ia') . PHP_EOL;
    echo 'Added Date: ' . $breach->getAddedDate()->format('d/m/Y') . PHP_EOL;
    echo 'Pwn Count: ' . $breach->getPwnCount() . PHP_EOL;
    echo 'Description: ' . $breach->getDescription() . PHP_EOL;
    echo 'Data Classes: ' . \implode(', ', $breach->getDataClasses()) . PHP_EOL;
    echo 'Verified?: ' . \var_export($breach->isVerified(), true) . PHP_EOL;
    echo 'Fabricated: ' . \var_export($breach->isFabricated(), true) . PHP_EOL;
    echo 'Sensitive?: ' . \var_export($breach->isSensitive(), true) . PHP_EOL;
    echo 'Retired?: ' . \var_export($breach->isRetired(), true) . PHP_EOL;
    echo 'Spam List?: ' . \var_export($breach->isSpamList(), true) . PHP_EOL;
    echo 'Logo Path: ' . $breach->getLogoPath() . PHP_EOL;
    echo '--------------------------------------------------------------------------------' . PHP_EOL;
}
