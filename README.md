[![Contributor Covenant](https://img.shields.io/badge/Contributor%20Covenant-v2.0%20adopted-ff69b4.svg)](code-of-conduct.md)

# Have I Been Pwned PHP

PHP 8.4 or above is required.

For PHP 8.2 and 8.3 please use 3.0.0. For PHP 8.1 please use 2.0.0. For PHP 7.4 to 8.0 please use version 1.2.0.

See [CHANGELOG.md](CHANGELOG.md) for a full history of changes between versions.

## Usage

Most methods require a Have I Been Pwned API key, obtained on a monthly subscription basis: https://haveibeenpwned.com/API/Key

HIBP has three subscription tiers - **Core**, **Pro** and **High RPM** - and a handful of endpoints are restricted to Pro and above regardless of tier. See [Plan-specific features](https://haveibeenpwned.com/API/v3#PlanSpecificFeatures) and the [pricing page](https://haveibeenpwned.com/Subscription) for full details. A few endpoints (breach sources, a single breach, the latest breach, data classes, and Pwned Passwords) are public and need no API key at all. Each method below states what it requires; the table is a quick reference and the sections beneath it show full usage examples.

| Method | Requires | Returns |
| --- | --- | --- |
| `BreachedAccount::breaches()` | API key (Core tier or above) | `Entity\Breach[]` |
| `BreachedAccount::breachNames()` | API key (Core tier or above) | `string[]` |
| `BreachedAccount::count()` | API key (Core tier or above) | `int` |
| `BreachedAccount::isBreached()` | API key (Core tier or above) | `bool` |
| `BreachedAccount::breachNamesByHashRange()` | API key, **Pro tier or above** | `string[]` |
| `Breaches::allSources()` | none (public) | `Entity\Breach[]` |
| `Breaches::byDomain()` | none (public) | `Entity\Breach[]` |
| `Breaches::bySourceName()` | none (public) | `?Entity\Breach` |
| `Breaches::latest()` | none (public) | `?Entity\Breach` |
| `Password::count()` | none (public) | `int` |
| `Password::isPwned()` | none (public) | `bool` |
| `PastedAccount::pastes()` | API key (Core tier or above) | `Entity\Paste[]` |
| `DataClasses::all()` | none (public) | `string[]` |
| `BreachedDomain::search()` | API key (Core tier or above) + verified domain | `array<string, string[]>` |
| `SubscribedDomains::all()` | API key (Core tier or above) | `Entity\SubscribedDomain[]` |
| `StealerLog::byEmail()` | API key, **Pro tier or above**, + verified domain | `string[]` |
| `StealerLog::byWebsiteDomain()` | API key, **Pro tier or above**, + verified domain | `string[]` |
| `StealerLog::byEmailDomain()` | API key, **Pro tier or above**, + verified domain | `array<string, string[]>` |
| `Subscription::status()` | API key (Core tier or above) | `Entity\SubscriptionStatus` |
| `DomainVerification::generateDnsToken()` | API key, **Pro tier or above** | `string` |
| `DomainVerification::verifyDnsToken()` | API key, **Pro tier or above** | `?string` |
| `DomainVerification::sendEmail()` | API key, **Pro tier or above** | `void` |

### Installation

To install this package via [Composer](https://getcomposer.org/):

```bash
composer require elliotjreed/haveibeenpwned
```

### Count of breaches by email address

*Requires an API key (Core tier or above).*

Return a count of all breaches for a specified email address (`int`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$count = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->count('email@example.com');
```

### Breaches by email address

*Requires an API key (Core tier or above).*

Return details of all breaches for a specified email address (`ElliotJReed\HaveIBeenPwned\Entity\Breach[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breaches = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->breaches('email@example.com');
```

### Breach names by email address

*Requires an API key (Core tier or above).*

Return the names of the breaches for a specified email address (`string[]`);

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachNames = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->breachNames('email@example.com');
```

The `breaches()`, `breachNames()` and `count()` methods all accept an optional `$domain` argument to filter results to a single breached site, e.g. `breaches('email@example.com', domain: 'adobe.com')`.

### Whether an email address has been breached

*Requires an API key (Core tier or above).*

Return whether a specified email address has appeared in any breach (`bool`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$isBreached = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->isBreached('email@example.com');
```

### Breach names by email address using k-anonymity

*Requires an API key on the **Pro** tier or above - this is a Pro-only endpoint even though the plain email search above works on Core. A Core-tier key will receive a 403 Forbidden response.*

Return the names of the breaches for a specified email address (`string[]`) without sending the full email address to the API - only the first 6 characters of its SHA-1 hash are sent.

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachNames = (new \ElliotJReed\HaveIBeenPwned\BreachedAccount($guzzle, $apiKey))->breachNamesByHashRange('email@example.com');
```

### Count of exposed passwords by password

*No API key or subscription required - the Pwned Passwords API is free and public. The `$apiKey` argument may be an empty string.*

Return a count of exposed passwords for a specified password (`int`).

Note: This API call DOES NOT send the actual password to the Have I Been Pwned API, see: https://haveibeenpwned.com/API/v3#PwnedPasswords.

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$count = (new \ElliotJReed\HaveIBeenPwned\Password($guzzle, $apiKey))->count('password123');
```

Pass `ntlm: true` to search NTLM hashes instead of SHA-1, and `addPadding: true` to request [padded responses](https://haveibeenpwned.com/API/v3#PwnedPasswordsPadding):

```php
$count = (new \ElliotJReed\HaveIBeenPwned\Password($guzzle, $apiKey))->count('password123', ntlm: true, addPadding: true);
```

### Whether a password has been exposed

*No API key or subscription required - the Pwned Passwords API is free and public.*

Return whether a specified password has appeared in a data breach (`bool`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$isPwned = (new \ElliotJReed\HaveIBeenPwned\Password($guzzle, $apiKey))->isPwned('password123');
```

### Pastes by email address

*Requires an API key (Core tier or above).*

Return details of a specified email address appearing on "pastes" online (`\ElliotJReed\HaveIBeenPwned\Entity\Paste[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$pastes = (new \ElliotJReed\HaveIBeenPwned\PastedAccount($guzzle, $apiKey))->pastes('email@example.com');
```

### Breach sources

*No API key required - this endpoint is free and public.*

Return all breach sources recorded by Have I Been Pwned (`\ElliotJReed\HaveIBeenPwned\Entity\Breach[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$allBreaches = (new \ElliotJReed\HaveIBeenPwned\Breaches($guzzle, $apiKey))->allSources();
```

`allSources()` accepts an optional `bool` argument to filter by whether a breach is flagged as a spam list, e.g. `allSources(isSpamList: true)`.

### The most recently added breach

*No API key required - this endpoint is free and public.*

Return the most recently added breach (`?ElliotJReed\HaveIBeenPwned\Entity\Breach`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$latestBreach = (new \ElliotJReed\HaveIBeenPwned\Breaches($guzzle, $apiKey))->latest();
```

### Breach source by name

*No API key required - this endpoint is free and public.*

Return breach details by source name (`\ElliotJReed\HaveIBeenPwned\Entity\Breach`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachesBySource = (new \ElliotJReed\HaveIBeenPwned\Breaches($guzzle, $apiKey))->bySourceName('Adobe');
```

### Breach source by domain

*No API key required - this endpoint is free and public.*

Return breach details by domain name (`\ElliotJReed\HaveIBeenPwned\Entity\Breach[]`) - a domain can appear in more than one breach.

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachesBySource = (new \ElliotJReed\HaveIBeenPwned\Breaches($guzzle, $apiKey))->byDomain('adobe.com');
```

### Data classes

*No API key required - this endpoint is free and public.*

Return the data classes used by Have I Been Pwned (`string[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$haveIBeenPwnedDataClasses = (new \ElliotJReed\HaveIBeenPwned\DataClasses($guzzle, $apiKey))->all();
```

### Domain search

*Requires an API key (Core tier or above). `BreachedDomain::search()` also requires the domain to first be verified via the [Have I Been Pwned dashboard](https://haveibeenpwned.com/Dashboard) or the domain verification APIs below - searching an unverified domain returns a 403 Forbidden response.*

Return all breached email aliases for a verified domain (`array<string, string[]>` mapping each alias to the breach names it appeared in).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$breachedAddresses = (new \ElliotJReed\HaveIBeenPwned\BreachedDomain($guzzle, $apiKey))->search('example.com');
```

Return all domains added to the domain search dashboard (`\ElliotJReed\HaveIBeenPwned\Entity\SubscribedDomain[]`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$subscribedDomains = (new \ElliotJReed\HaveIBeenPwned\SubscribedDomains($guzzle, $apiKey))->all();
```

### Domain verification

*Requires an API key on the **Pro** tier or above.*

Generate a DNS TXT record value to verify ownership of a domain (`string`), then verify it once the record has been set (`?string`, containing a failure reason, or `null` on success), or send a verification email instead.

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$domainVerification = new \ElliotJReed\HaveIBeenPwned\DomainVerification($guzzle, $apiKey);

$txtRecordValue = $domainVerification->generateDnsToken('example.com');
$failureReason = $domainVerification->verifyDnsToken('example.com');

$domainVerification->sendEmail('example.com', 'admin');
```

### Stealer logs

*Requires an API key on the **Pro** tier or above, regardless of domain size. Stealer log searches also require the relevant domain to first be verified via the [Have I Been Pwned dashboard](https://haveibeenpwned.com/Dashboard) - searching an unverified domain returns a 403 Forbidden response.*

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$stealerLog = new \ElliotJReed\HaveIBeenPwned\StealerLog($guzzle, $apiKey);

$websiteDomains = $stealerLog->byEmail('jane@example.com');
$emailAddresses = $stealerLog->byWebsiteDomain('netflix.com');
$emailAliases = $stealerLog->byEmailDomain('example.com');
```

### Subscription status

*Requires an API key (Core tier or above).*

Return details of the current API key's subscription (`\ElliotJReed\HaveIBeenPwned\Entity\SubscriptionStatus`).

```php
$guzzle = new \GuzzleHttp\Client();
$apiKey = 'HIBP-API-KEY';

$subscriptionStatus = (new \ElliotJReed\HaveIBeenPwned\Subscription($guzzle, $apiKey))->status();
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

Code format checking (via [PHP Code Sniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer)) can be run by executing:

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
- [PHP Code Sniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer)
- [GNU Make](https://www.gnu.org/software/make/)

## License

This project is licensed under the MIT License - see the [LICENCE.md](LICENCE.md) file for details.
