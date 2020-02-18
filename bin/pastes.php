#!/usr/bin/env php
<?php

declare(strict_types=1);

use ElliotJReed\HaveIBeenPwned\PastedAccount;
use GuzzleHttp\Client;

if (\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) === false) {
    echo 'Error: This command should be invoked via the CLI version of PHP, not the ' . \PHP_SAPI . ' SAPI' . \PHP_EOL;
    exit(1);
}

if ($argc === 1) {
    echo "Error: An email address argument is required (e.g. ./pastes.php 'email@example.com')" . \PHP_EOL;
    exit(1);
}

if ($argc > 2) {
    echo "Warning: This application requires a single argument (e.g. ./pastes.php 'email@example.com'). Additional arguments will be ignored." . \PHP_EOL;
}

require __DIR__ . '/../vendor/autoload.php';

$guzzle = new Client();
$apiKey = 'YOUR_API_KEY';

foreach ((new PastedAccount($guzzle, $apiKey))->all($argv[1]) as $breach) {
    echo 'Source: ' . $breach->getSource() . PHP_EOL;
    echo 'ID: ' . $breach->getId() . PHP_EOL;
    echo 'Title: ' . $breach->getTitle() . PHP_EOL;
    echo 'Date: ' . $breach->getDate()->format('d/m/Y g:ia') . PHP_EOL;
    echo 'Email Count: ' . $breach->getEmailCount() . PHP_EOL;
    echo '--------------------------------------------------------------------------------' . PHP_EOL;
}
