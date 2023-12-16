[![Contributor Covenant](https://img.shields.io/badge/Contributor%20Covenant-v2.0%20adopted-ff69b4.svg)](code-of-conduct.md)

# Have I Been Pwned PHP

PHP 8.1 or above is required. For PHP 7.4 to 8.0 please use verison 1.2.x.

## Usage

A Have I Been Pwned API key is required. This can be obtained on a monthly subscription basis, or a one-off monthly access charge.

https://haveibeenpwned.com/API/v3#

### Installation

To install this package via [Composer](https://getcomposer.org/):

```bash
composer require elliotjreed/haveibeenpwned
```

### Count of breaches by email address

Return a count of all breaches for a specified email address (`int`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$count = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->count('email@example.com');
```

### Breaches by email address

Return details of all breaches for a specified email address (`ElliotJReed\HaveIBeenPwned\Entity\Breach[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breaches = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->breaches('email@example.com');
```

### Breach names by email address

Return the names of the breaches for a specified email address (`string[]`);

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachNames = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->breachNames('email@example.com');
```

### Count of exposed passwords by password

Return a count of exposed passwords for a specified password (`int`).

Note: This API call DOES NOT send the actual password to the Have I Been Pwned API, see: https://haveibeenpwned.com/API/v3#PwnedPasswords.

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$count = (new \ElliotJReed\HaveIBeenPwned\Password($guzzle, $apiKey))->count('password123');
```

### Pastes by email address

Return details of a specified email address appearing on "pastes" online (`\ElliotJReed\HaveIBeenPwned\Builder\Paste[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$pastes = (new \ElliotJReed\HaveIBeenPwned\PastedAccount($guzzle, $apiKey))->pastes('email@example.com');
```

### Breach sources

Return all breach sources recorded by Have I Been Pwned (`\ElliotJReed\HaveIBeenPwned\Entity\Breach[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$allBreaches = (new \ElliotJReed\HaveIBeenPwned\Breaches($guzzle, $apiKey))->allSources();
```

### Breach source by name

Return breach details by source name (`\ElliotJReed\HaveIBeenPwned\Entity\Breach`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachesBySource = (new \ElliotJReed\HaveIBeenPwned\Breaches($guzzle, $apiKey))->bySourceName('Adobe');
```

### Breach source by domain

Return breach details by domain name (`\ElliotJReed\HaveIBeenPwned\Entity\Breach`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachesBySource = (new \ElliotJReed\HaveIBeenPwned\Breaches($guzzle, $apiKey))->byDomain('adobe.com');
```

### Data classes

Return the data classes used by Have I Been Pwned (`string[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$haveIBeenPwnedDataClasses = (new \ElliotJReed\HaveIBeenPwned\DataClasses($guzzle, $apiKey))->all();
```


## Development

PHP 7.4 or 8.0 and Composer is expected to be installed.

### Installing Composer

For instructions on how to install Composer visit [getcomposer.org](https://getcomposer.org/download/).

### Installing

After cloning this repository, change into the newly created directory and run:

```bash
composer install
```

or if you have installed Composer locally in your current directory:

```bash
php composer.phar install
```

This will install all dependencies needed for the project.

Henceforth, the rest of this README will assume `composer` is installed globally (ie. if you are using `composer.phar` you will need to use `composer.phar` instead of `composer` in your terminal / command-line).

## Running the Tests

### Unit tests

Unit testing in this project is via [PHPUnit](https://phpunit.de/).

All unit tests can be run by executing:

```bash
composer phpunit
```

#### Debugging

To have PHPUnit stop and report on the first failing test encountered, run:

```bash
composer phpunit:debug
```

### Static analysis

Static analysis tools can point to potential "weak spots" in your code, and can be useful in identifying unexpected side-effects.

[Psalm](https://psalm.dev/) is configured at it's highest levels, meaning false positives are quite likely.

All static analysis tests can be run by executing:

```bash
composer static-analysis
```

## Code formatting

A standard for code style can be important when working in teams, as it means that less time is spent by developers processing what they are reading (as everything will be consistent).

Code format checking (via [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)) can be run by executing:

```bash
composer phpcs
```

### Running everything

All of the tests can be run by executing:

```bash
composer test
```

### Outdated dependencies

Checking for outdated Composer dependencies can be performed by executing:

```bash
composer outdated
```

### Validating Composer configuration

Checking that the [composer.json](composer.json) is valid can be performed by executing:

```bash
composer validate --no-check-publish
```

### Running via GNU Make

If GNU [Make](https://www.gnu.org/software/make/) is installed, you can replace the above `composer` command prefixes with `make`.

All of the tests can be run by executing:

```bash
make test
```

### Running the tests on a Continuous Integration platform (eg. Travis)

To run all the tests and report code coverage in Clover XML format (which many CI platforms can read, including Travis CI), add the following to your CI config (eg. [.travis.yml](.travis.yml)):

```yaml
  script:
    - composer ci
```

## Coding standards

PHP coding standards are quite strict and are defined in [ruleset.xml](ruleset.xml).

The rules are PSR-2 and PSR-12 standards with additionally defined rules.

The code formatting checks can be run by executing:

```bash
composer phpcs
```

To automatically fix any issues where possible, run:

```bash
composer phpcs:fix
```

## Built With

- [PHP](https://secure.php.net/)
- [Composer](https://getcomposer.org/)
- [PHPUnit](https://phpunit.de/)
- [Psalm](https://psalm.dev/)
- [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- [GNU Make](https://www.gnu.org/software/make/)

## License

This project is licensed under the MIT License - see the [LICENCE.md](LICENCE.md) file for details.
