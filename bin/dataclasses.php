#!/usr/bin/env php
<?php

declare(strict_types=1);

use ElliotJReed\HaveIBeenPwned\DataClasses;
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

foreach ((new DataClasses($guzzle, $apiKey))->all() as $dataClass) {
    echo $dataClass . PHP_EOL;
}
