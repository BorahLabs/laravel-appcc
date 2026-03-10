# Laravel APPCC

A fluent, type-safe PHP client for the [APPCC Digital](https://appcc.digital) API. Record food safety logs (temperature, cleaning, reception, etc.) against an APPCC Digital tenant from any Laravel application.

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Installation

```bash
composer require appcc-digital/laravel-appcc
```

Auto-discovery registers the service provider. Publish the config file:

```bash
php artisan vendor:publish --tag=appcc-config
```

Add these to your `.env`:

```env
APPCC_URL=https://mi-restaurante.appcc.digital
APPCC_TOKEN=your-bearer-token
APPCC_TIMEOUT=30
```

The `APPCC_URL` is your tenant's subdomain URL — the SDK appends `/api/v1` automatically.

## Verify Connection

```bash
php artisan appcc:test-connection
```

## Usage

All calls start from the `Appcc` facade:

```php
use AppccDigital\LaravelAppcc\Facades\Appcc;
```

### Temperature Logs

```php
$result = Appcc::temperatureLogs()
    ->for($equipmentId)
    ->record([
        'temperature' => 3.5,
        'corrective_action' => null,
    ], photo: '/path/to/photo.jpg');

$result->successful();           // bool
$result->data()->id;             // string (ULID)
$result->data()->temperature;    // float
$result->data()->isWithinLimits; // bool
```

### Cleaning Logs

```php
$result = Appcc::cleaningLogs()->record([
    'zone_id' => $zoneId,
    'type' => 'daily',              // daily|preventive|verification
    'evaluation' => 'correct',      // clean|correct|incorrect|very_dirty
    'cleaning_date' => '2026-03-10',
    'observations' => null,
    'corrective_action' => null,    // required if incorrect/very_dirty
    'items' => [],                  // required if type=verification
]);
```

### Incident Logs

```php
$result = Appcc::incidentLogs()->record([
    'element_involved' => 'Horno industrial',
    'incident_description' => 'Fallo eléctrico en resistencia superior',
    'corrective_action' => 'Se reemplazó la resistencia',
    'incident_date' => '2026-03-10',
    'resolution_date' => '2026-03-11',
    'observations' => null,
]);
```

### Vacuum Packaging Logs

```php
$result = Appcc::vacuumPackagingLogs()->record([
    'product_temp_before' => 4.0,
    'seal_integrity_ok' => true,
    'labeled_correctly' => true,
    'product_id' => $productId,
    'lot_reference' => 'LOT-2026-001',
]);
// expiry_date is auto-calculated server-side
```

### Reception Logs

```php
$result = Appcc::receptionLogs()->record([
    'supplier_id' => $supplierId,
    'product_name' => 'Pollo fresco',
    'visual_evaluation' => 'ok',       // ok|rejected
    'temperature' => 2.5,
    'packaging_ok' => true,
    'labeling_ok' => true,
    'allergens_ok' => true,
    'rejection_reason' => null,        // required if rejected
    'corrective_action' => null,       // required if rejected
], photo: $request->file('photo'));
```

### Blast Chiller Logs

Multi-step process:

```php
// 1. Start cycle
$result = Appcc::blastChillerLogs()->record([
    'entry_temp' => 65.0,
    'product_id' => $productId,
    'equipment_id' => $equipmentId,
]);
$logId = $result->data()->id;

// 2. Record midpoint
$result = Appcc::blastChillerLogs()->for($logId)->update([
    'mid_temp' => 30.0,
]);

// 3. Record exit
$result = Appcc::blastChillerLogs()->for($logId)->update([
    'exit_temp' => 3.0,
    'corrective_action' => null, // required if out of limits
]);
```

### Water Control Logs

```php
$result = Appcc::waterControlLogs()->record([
    'sample_location' => 'Cocina principal',
    'chlorine_level' => 0.5,   // OK: 0.2–1.0 mg/L
    'ph_level' => 7.2,         // OK: 6.5–9.5
    'corrective_action' => null,
]);
```

### Vegetable Disinfection Logs

```php
$result = Appcc::vegetableDisinfectionLogs()->record([
    'product_name' => 'Lechuga',
    'start_time' => '10:00',
    'end_time' => '10:15',
    'chlorine_concentration_ppm' => 100,
]);
```

### Sample Storage Logs

```php
$result = Appcc::sampleStorageLogs()->record([
    'product_name' => 'Paella',
    'quantity_grams' => 150,
    'storage_temp' => -18.0,
    'container_type' => 'Bolsa hermética',
]);
// destruction_date is auto-calculated (sample_date + 7 days)
```

## Result Object

All API calls return an `AppccResult` instance:

```php
$result->successful();  // bool — true for 2xx
$result->failed();      // bool — true for non-2xx
$result->status();      // int — HTTP status code
$result->data();        // Typed DTO on success, null on failure
$result->errors();      // Validation errors on 422, empty otherwise
$result->message();     // Error message from API
$result->toArray();     // Raw response array
```

No exceptions are thrown — the caller always gets an `AppccResult` and decides how to handle failures.

## Queued Dispatch

Fire-and-forget any API call via Laravel's queue system:

```php
// Synchronous (default)
$result = Appcc::temperatureLogs()->for($eq)->record([...]);

// Queued
Appcc::temperatureLogs()->for($eq)->record([...])->dispatch();

// Custom queue
Appcc::temperatureLogs()->for($eq)->record([...])->dispatch(queue: 'sensors');
```

Configure the default queue in `config/appcc.php` or via `APPCC_QUEUE` env var.

## File Uploads

Endpoints that accept photos (`temperature-logs`, `reception-logs`) support file uploads:

```php
// File path
Appcc::temperatureLogs()->for($eq)->record($data, photo: '/tmp/photo.jpg');

// Laravel UploadedFile
Appcc::temperatureLogs()->for($eq)->record($data, photo: $request->file('photo'));

// Resource/stream
Appcc::temperatureLogs()->for($eq)->record($data, photo: fopen('/tmp/photo.jpg', 'r'));
```

The SDK automatically switches to `multipart/form-data` when a file is attached.

## Testing

### Faking the Client

```php
use AppccDigital\LaravelAppcc\Facades\Appcc;

beforeEach(function () {
    Appcc::fake();
});

it('records temperature after sensor read', function () {
    // ... your application code ...

    Appcc::assertSent('temperature-logs', 1);
    Appcc::assertSentWith('temperature-logs', [
        'temperature' => 3.5,
    ]);
});
```

### Assertions

```php
Appcc::assertSent('temperature-logs', 2);      // called N times
Appcc::assertSent('cleaning-logs');             // at least once
Appcc::assertSentWith('temperature-logs', [...]);
Appcc::assertNotSent('incident-logs');
Appcc::assertNothingSent();
Appcc::assertNothingSentTo('incident-logs');

$requests = Appcc::recorded();                  // all requests
$requests = Appcc::recorded('temperature-logs'); // filtered
```

### Custom Fake Responses

```php
Appcc::fake([
    'temperature-logs' => Appcc::response([
        'id' => 'fake-id',
        'temperature' => 3.5,
        'is_within_limits' => true,
    ], 201),

    'incident-logs' => Appcc::response([
        'message' => 'Validation failed',
        'errors' => ['element_involved' => ['Required']],
    ], 422),
]);
```

## Endpoints Reference

| SDK Method | HTTP | API Endpoint |
|---|---|---|
| `temperatureLogs()->for($eq)->record()` | POST | `/api/v1/temperature-logs/{equipment}` |
| `cleaningLogs()->record()` | POST | `/api/v1/cleaning-logs` |
| `incidentLogs()->record()` | POST | `/api/v1/incident-logs` |
| `vacuumPackagingLogs()->record()` | POST | `/api/v1/vacuum-packaging-logs` |
| `receptionLogs()->record()` | POST | `/api/v1/reception-logs` |
| `blastChillerLogs()->record()` | POST | `/api/v1/blast-chiller-logs` |
| `blastChillerLogs()->for($id)->update()` | PATCH | `/api/v1/blast-chiller-logs/{log}` |
| `waterControlLogs()->record()` | POST | `/api/v1/water-control-logs` |
| `vegetableDisinfectionLogs()->record()` | POST | `/api/v1/vegetable-disinfection-logs` |
| `sampleStorageLogs()->record()` | POST | `/api/v1/sample-storage-logs` |

## License

MIT
