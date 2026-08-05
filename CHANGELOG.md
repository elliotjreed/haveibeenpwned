# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). The installable package version is determined entirely by the [Git tags](https://github.com/elliotjreed/haveibeenpwned/tags) on this repository - there is no `version` field in `composer.json`.

## [4.1.0] - 2026-08-05

Fixes `Api::handleRequestException()` for compatibility with Guzzle 8, where `RequestException::hasResponse()` and the nullable `getResponse()` were removed from the base exception class. Detection now uses `instanceof BadResponseException`, which reliably carries a response on both Guzzle 7 and 8.

## [4.0.1] - 2026-07-24

Fixes coverage check in GitHub Actions.

## [4.0.0] - 2026-07-24

### Added

- Full coverage of the Have I Been Pwned v3 API:
  - `Breaches::latest()` - the most recently added breach.
  - `BreachedAccount::breachNamesByHashRange()` - k-anonymity email breach search (only the first 6 characters of the SHA-1 hash are sent).
  - `BreachedAccount::isBreached()` - boolean convenience wrapper over `count()`.
  - `Password::isPwned()` - boolean convenience wrapper over `count()`.
  - `BreachedDomain` and `SubscribedDomains` - domain search.
  - `DomainVerification` - DNS and email domain verification (the only POST endpoints).
  - `StealerLog` - stealer log search by email, website domain, and email domain.
  - `Subscription` - subscription status.
- `BreachedAccount::breaches()`, `breachNames()` and `count()` now accept an optional `$domain` argument to filter to a single breached site.
- `Breaches::allSources()` now accepts an optional `$isSpamList` argument.
- `Password::count()` now accepts `$ntlm` (search NTLM hashes instead of SHA-1) and `$addPadding` ([padded responses](https://haveibeenpwned.com/API/v3#PwnedPasswordsPadding)) arguments.
- `Entity\Breach` gained `isMalware()`, `isSubscriptionFree()`, `isStealerLog()` and `getAttribution()`, matching fields added to the HIBP breach model since this library was first written.
- `Entity\Breach` and `Entity\Paste` now implement `JsonSerializable` and expose `toArray()`.
- `Exception\TooManyRequests::getRetryAfter()` exposes the `retry-after` header from HIBP's 429 responses, in seconds.
- `Exception\BadRequest`, `Unauthorised`, `Forbidden`, `NotFound` and `ServiceUnavailable` now surface the real `message` from HIBP's error response body when one is present, rather than always showing a generic default - a 403 that was actually caused by an insufficient subscription tier no longer misreports itself as a missing user agent, for example.

### Fixed

- `BreachedAccount::breaches()`, `breachNames()` and `count()` no longer send a malformed query string (`&?includeUnverified=false`) when `$unverified` is `false`.
- Fixed the PHP_CodeSniffer repository link in the README (#4, thanks @rodrigoprimo).

### Changed

- Minimum PHP version raised to 8.4 (dropping PHP 8.2 and 8.3 support); CI now tests against PHP 8.4 and 8.5.
- Added `ext-mbstring` as a required extension, used for NTLM password hashing.
- `Api` gained internal POST support (`postToBreachApi()`) alongside the existing GET support, used by the new `DomainVerification` endpoints.

## [3.0.0] - 2024-12-15

### Changed

- Minimum PHP version raised to 8.2 (dropping PHP 8.1 support).
- CI now tests against PHP 8.4.
- Reformatted the codebase to satisfy the latest PHP-CS-Fixer rules.
- Dropped Psalm as a development dependency.

## [2.0.0] - 2023-12-16

### Changed

- Minimum PHP version raised to 8.1 (dropping PHP 7.4 and PHP 8.0 support).

## [1.2.0] - 2023-01-20

### Added

- PHP 8.2 support.

## [1.1.0] - 2022-07-15

### Fixed

- `Password::count()` no longer errors on blank or malformed lines in the Pwned Passwords range response.

### Changed

- Switched to PHP-CS-Fixer for code formatting.

## [1.0.5] - 2021-08-08

### Changed

- Dependency updates; `psr/log` versions 1 and 2 are both allowed, for PHP 7.4 compatibility.

## [1.0.4] - 2021-05-09

### Changed

- Dependency updates.

## [1.0.3] - 2021-04-22

Earliest tagged release. Established the architecture the library has followed since: a Guzzle-based HTTP client, one client class per HIBP endpoint area (`BreachedAccount`, `Breaches`, `PastedAccount`, `DataClasses`, `Password`), and Builder/Entity response objects.

[4.0.0]: https://github.com/elliotjreed/haveibeenpwned/compare/3.0.0...HEAD
[3.0.0]: https://github.com/elliotjreed/haveibeenpwned/compare/2.0.0...3.0.0
[2.0.0]: https://github.com/elliotjreed/haveibeenpwned/compare/1.2.0...2.0.0
[1.2.0]: https://github.com/elliotjreed/haveibeenpwned/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/elliotjreed/haveibeenpwned/compare/1.0.5...1.1.0
[1.0.5]: https://github.com/elliotjreed/haveibeenpwned/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/elliotjreed/haveibeenpwned/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/elliotjreed/haveibeenpwned/releases/tag/1.0.3
