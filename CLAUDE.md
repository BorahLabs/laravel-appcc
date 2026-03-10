# Laravel APPCC — SDK Package

## What This Is

A standalone Composer package (`appcc-digital/laravel-appcc`) that provides a fluent PHP client for the APPCC Digital API. It is consumed by external Laravel apps to record food safety logs against a tenant.

## Project Structure

```
src/
├── AppccManager.php                # Main entry point, creates resource builders
├── AppccServiceProvider.php        # Service provider + config publishing
├── Facades/Appcc.php               # Facade with fake() and response() static methods
├── Http/AppccClient.php            # Wraps Laravel Http, handles auth/base URL/multipart
├── Resources/                      # Fluent builders (one per endpoint)
│   ├── TemperatureLogResource.php
│   ├── CleaningLogResource.php
│   ├── IncidentLogResource.php
│   ├── VacuumPackagingLogResource.php
│   ├── ReceptionLogResource.php
│   ├── BlastChillerLogResource.php  # Has both record() and update()
│   ├── WaterControlLogResource.php
│   ├── VegetableDisinfectionLogResource.php
│   └── SampleStorageLogResource.php
├── Data/                           # Readonly DTOs with fromArray() factories
├── Results/AppccResult.php         # Generic result wrapper (success/failure/data/errors)
├── Jobs/SendAppccRequest.php       # Queued job for dispatch() support
├── Testing/
│   ├── AppccFake.php               # Fake manager with assertion methods
│   ├── FakeResource.php            # Fake resource builder
│   └── AppccFakeResponse.php       # Custom response DTO for faking
└── Commands/TestConnectionCommand.php
```

## Key Patterns

- **No exceptions thrown** — all API calls return `AppccResult` and the caller handles failures.
- **Fluent builders** — `Appcc::temperatureLogs()->for($eq)->record([...])`.
- **Generic result** — `AppccResult<T>` uses `withDto()` to map responses to typed DTOs.
- **File uploads** — `AppccClient` auto-switches to multipart when a photo is passed. Only `temperature-logs` and `reception-logs` support photos.
- **Blast chiller is special** — it's the only resource with both `record()` (POST) and `update()` (PATCH) methods.
- **Fake system** — `Appcc::fake()` swaps the facade root with `AppccFake`, which uses `FakeResource` internally. `recordRequest()` takes both a base endpoint (for keying/assertions) and a full endpoint (for the recorded entry).

## Testing

- Uses Pest + Orchestra Testbench.
- Run tests: `vendor/bin/pest`
- Test config is set in `tests/TestCase.php::defineEnvironment()`.

## API It Talks To

The APPCC Digital API lives at `{tenant-url}/api/v1/`. Auth is Bearer token on the `Authorization` header. All responses wrap data in `{"data": {...}}`. Validation errors return 422 with `{"message": "...", "errors": {...}}`.

The API spec is defined in the main app's `public/openapi.yaml` at `/Users/raullg/GitHub/appc-digital/public/openapi.yaml`.

## Dependencies

- PHP 8.2+
- Laravel 11+ (illuminate/http, illuminate/support, illuminate/contracts)
- Dev: orchestra/testbench, pestphp/pest, phpstan/phpstan

## Conventions

- DTOs are `readonly` classes with `public` constructor promotion.
- Resources store pending data and delegate to `AppccClient`.
- Snake_case from API → camelCase in DTOs via `fromArray()`.
- Config uses `env()` only in config file, never directly in code.
