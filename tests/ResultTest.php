<?php

use AppccDigital\LaravelAppcc\Data\TemperatureLogResult;
use AppccDigital\LaravelAppcc\Results\AppccResult;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Response as ClientResponse;

function makeResult(array $body, int $status = 200): AppccResult
{
    $psrResponse = new Response(
        $status,
        ['Content-Type' => 'application/json'],
        json_encode($body),
    );

    return new AppccResult(new ClientResponse($psrResponse));
}

it('reports success for 2xx responses', function () {
    $result = makeResult(['data' => ['id' => '123']], 201);

    expect($result->successful())->toBeTrue();
    expect($result->failed())->toBeFalse();
    expect($result->status())->toBe(201);
});

it('reports failure for non-2xx responses', function () {
    $result = makeResult(['message' => 'Token inválido.'], 401);

    expect($result->failed())->toBeTrue();
    expect($result->successful())->toBeFalse();
    expect($result->message())->toBe('Token inválido.');
});

it('returns validation errors for 422 responses', function () {
    $result = makeResult([
        'message' => 'Validation failed',
        'errors' => ['temperature' => ['Required field']],
    ], 422);

    expect($result->errors())->toBe(['temperature' => ['Required field']]);
    expect($result->data())->toBeNull();
});

it('returns empty errors for non-422 responses', function () {
    $result = makeResult(['data' => []], 200);

    expect($result->errors())->toBe([]);
});

it('maps data to typed DTO', function () {
    $result = makeResult([
        'data' => [
            'id' => '01HTEST123',
            'tenant_id' => '01HTENANT',
            'equipment_id' => '01HEQ123',
            'recorded_by' => null,
            'temperature' => 3.5,
            'is_within_limits' => true,
            'corrective_action' => null,
            'recorded_at' => '2026-03-10T10:00:00Z',
            'created_at' => '2026-03-10T10:00:00Z',
        ],
    ], 201);

    $result->withDto(TemperatureLogResult::class);

    $data = $result->data();

    expect($data)->toBeInstanceOf(TemperatureLogResult::class);
    expect($data->id)->toBe('01HTEST123');
    expect($data->temperature)->toBe(3.5);
    expect($data->isWithinLimits)->toBeTrue();
    expect($data->recordedBy)->toBeNull();
});

it('returns raw array when no DTO class set', function () {
    $result = makeResult(['data' => ['id' => '123', 'foo' => 'bar']], 200);

    expect($result->data())->toBe(['id' => '123', 'foo' => 'bar']);
});

it('returns full response as array', function () {
    $result = makeResult(['data' => ['id' => '123'], 'meta' => 'test'], 200);

    expect($result->toArray())->toBe(['data' => ['id' => '123'], 'meta' => 'test']);
});
