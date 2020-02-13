[![Contributor Covenant](https://img.shields.io/badge/Contributor%20Covenant-v2.0%20adopted-ff69b4.svg)](code-of-conduct.md) [![Build Status](https://travis-ci.org/elliotjreed/haveibeenpwned-filter-php.svg?branch=master)](https://travis-ci.org/elliotjreed/haveibeenpwned-filter-php) [![Coverage Status](https://coveralls.io/repos/github/elliotjreed/haveibeenpwned-filter-php/badge.svg?branch=master)](https://coveralls.io/github/elliotjreed/haveibeenpwned-filter-php?branch=master)

# haveibeenpwned-filter-php

## Getting Started

PHP 7.4 or above and Composer is expected to be installed.

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

[Phan](https://github.com/phan/phan) and [Psalm](https://psalm.dev/) are configured at their highest levels, meaning false positives are quite likely.

All static analysis tests can be run by executing:

```bash
composer static-analysis
```

### Mutation testing

Mutation testing provides an indication as to how "robust" your unit tests are by changing small bits of code (eg. `$x > 1` could be changed to `$x >= 1`) and seeing if your tests still pass - if they do, your tests are likely a bit flaky.

Mutation testing (via [Infection](https://infection.github.io/)) can be run by executing:

```bash
composer mutation
```

### Mess detection

Mess detection can look for potential problems such as bugs, suboptimal code, overcomplicated expressions, and unused parameters, method, and properties.

Mess detection (via [PHPMD](https://phpmd.org/)) can be run by executing:

```bash
composer phpmd
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
    - composer travis
  after_success:
    - travis_retry php vendor/bin/php-coveralls
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
  - [Phan](https://github.com/phan/phan)
  - [Psalm](https://psalm.dev/)
  - [PHPMD](https://phpmd.org/)
  - [Infection](https://infection.github.io/)
  - [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
  - [GNU Make](https://www.gnu.org/software/make/)

## License

This project is licensed under the MIT License - see the [LICENCE.md](LICENCE.md) file for details.
