# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

A PHP library wrapping the Have I Been Pwned v3 API (`elliotjreed/haveibeenpwned`). It exposes small, single-purpose client classes for each HIBP endpoint (breached accounts, pwned passwords, pastes, breach/domain search, stealer logs, subscription status, domain verification) built on top of Guzzle. PHP 8.4+ is required.

## Commands

Run everything via Composer (a `Makefile` wraps the same scripts if `make` is preferred - `make test`, `make phpunit`, `make phpcs`, `make debug`):

```bash
composer install                # install dependencies

composer phpunit                # run the full PHPUnit suite
composer phpunit:debug          # stop on first failure
composer phpunit:coverage       # run with HTML + text coverage report (./coverage)
composer phpunit:ci             # run with Clover XML coverage (./build/logs/clover.xml)

composer phpcs                  # PHP-CS-Fixer (auto-fixes) then PHP_CodeSniffer (check only)

composer test                   # phpunit:coverage + phpcs
composer ci                     # phpunit:ci + phpcs (what CI runs)
```

To run a single test file or method directly:

```bash
vendor/bin/phpunit -c phpunit.xml tests/ElliotJReed/HaveIBeenPwned/PasswordTest.php
vendor/bin/phpunit -c phpunit.xml --filter testItReturnsCountOfBreaches
```

### CI requires 100% code coverage

`.github/workflows/main.yml` runs PHPUnit with `--coverage-text` and **fails the build if lines, methods, or classes coverage is anything other than 100.00%**. Any new code must be fully covered by tests - there is no partial-coverage allowance. CI also runs `php-cs-fixer fix --dry-run` (fails on any formatting diff) against PHP 8.4 and 8.5.

## Architecture

### Request flow

`Api` (`src/ElliotJReed/HaveIBeenPwned/Api.php`) is an abstract base class all client classes extend. It holds the Guzzle `ClientInterface` and API key, and centralises:

- `queryBreachApi(endPoint, baseUri, headers)` - sends a GET request (via private `call`/`sendRequest`) and returns the response body stream; `headers` merges in extras like `Add-Padding`.
- `postToBreachApi(endPoint, body, baseUri)` - sends a POST request with a JSON body (used only by `DomainVerification`).
- `encodeUrl()` - lowercases, trims, and URL-encodes user input (emails, domains, source names) before it goes into the path. Never apply this to values going into a POST JSON body (would double-encode) - pass those straight through.
- HTTP status → exception mapping in `handleNotOkResponse()`: 400 → `BadRequest`, 401 → `Unauthorised`, 403 → `Forbidden`, 404 → `NotFound`, 429 → `TooManyRequests` (constructed with the `retry-after` response header, exposed via `getRetryAfter(): ?int`), 503 → `ServiceUnavailable`, anything else → `UnknownServerError`. All exceptions live in `src/ElliotJReed/HaveIBeenPwned/Exception/` and implement the marker interface `Exception\HaveIBeenPwned`. Both `handleNotOkResponse()` and `handleRequestException()` are typed `: never` since every branch throws.
- `NotFound` is treated as "no results" by convention, not an error: most public client methods catch it internally and return an empty array / null / zero rather than letting it propagate. Endpoints the live docs don't document as ever 404ing (e.g. `DataClasses::all()`, `SubscribedDomains::all()`, the k-anonymity range search) deliberately have no catch - don't add one without a documented reason, since an untriggerable catch block breaks the 100% coverage gate.

Each public client class extends `Api` and implements one area of the HIBP API surface: `BreachedAccount`, `Breaches`, `PastedAccount`, `DataClasses`, `Password` (GET, original endpoints), plus `BreachedDomain`, `SubscribedDomains`, `StealerLog`, `Subscription` (GET, Pro-tier) and `DomainVerification` (the only POST endpoints). `Password` overrides the base URI to `api.pwnedpasswords.com` and implements the k-anonymity range search itself (hashes the password with SHA-1 or NTLM, queries the first 5 hex characters, matches the suffix client-side) - the plaintext password is never sent over the network. `BreachedAccount::breachNamesByHashRange()` does the equivalent 6-character k-anonymity search for email breaches.

### Builder / Entity split

Raw JSON responses are never returned directly for endpoints with a well-defined object shape. Two parallel object graphs exist:

- `Entity\*` (`Breach`, `Paste`, `SubscribedDomain`, `SubscriptionStatus`) - plain data objects with private typed properties and fluent `getX()`/`setX()` methods (in `src/ElliotJReed/HaveIBeenPwned/Entity/`). `Breach` and `Paste` additionally implement `JsonSerializable` (`toArray()` + `jsonSerialize()`) for easy re-encoding/logging.
- `Builder\*` - stateless factories with a single static `build(array $data): Entity` method that maps a decoded JSON associative array onto the corresponding Entity (in `src/ElliotJReed/HaveIBeenPwned/Builder/`). Optional/nullable API fields are read with `?? null`, matching the field's entity type.

When adding a new endpoint that returns a well-defined structured response, follow this same pattern rather than returning arrays directly. Endpoints whose response is just a scalar-keyed map with no fixed shape (`BreachedDomain::search()`, `StealerLog::byEmailDomain()`) return plain arrays instead - there's no entity to model.

### Testing conventions

Tests mirror the `src/` namespace 1:1 under `tests/ElliotJReed/HaveIBeenPwned/` (namespace `Tests\ElliotJReed\HaveIBeenPwned`), **except** `Entity\*`/`Builder\*`, which have no dedicated test files - they're exercised through the client-class tests that consume them (e.g. `Entity\Breach` getters/serialisation are asserted inside `BreachedAccountTest`/`BreachesTest`). HTTP is faked with Guzzle's `MockHandler` + `HandlerStack` - no real network calls, no mocking framework. Tests typically assert both the returned value/entity *and* the outgoing request shape (method, host, path, query string, headers, and - for `DomainVerification` - the POST JSON body). `tests/.../Double/ApiCallDummy.php` is a minimal concrete subclass of the abstract `Api` used solely to exercise `Api`'s own protected methods (`queryBreachApi` with extra headers, `postToBreachApi`) in `ApiTest.php`.

Remember to run `composer dump-autoload` after adding a new class - `classmap-authoritative` in `composer.json` means the autoloader won't pick up new files otherwise, and tests will fail with "Class not found" even though the code is correct.

## Code style

- `declare(strict_types=1)` in every file.
- PHP-CS-Fixer rules (`.php-cs-fixer.dist.php`): `@PSR2`, `@PSR12:risky`, `@Symfony`, `@Symfony:risky`, plus native function invocation is forced to the global namespace (`\json_decode`, `\strtoupper`, etc.) - always backslash-prefix core PHP functions.
- PHP_CodeSniffer (`ruleset.xml`) enforces PSR2 + PSR12 (with line-length checks excluded).
- British English spelling in code and docs (e.g. "Unauthorised", "licence").
