<?php

use AppccDigital\LaravelAppcc\Http\AppccClient;
use Illuminate\Support\Facades\Http;

it('sends POST requests with correct auth and url', function () {
    Http::fake([
        'https://test.appcc.digital/api/v1/temperature-logs/eq-123' => Http::response(['data' => ['id' => '1']], 201),
    ]);

    $client = new AppccClient(
        url: 'https://test.appcc.digital',
        token: 'test-token',
        timeout: 10,
    );

    $result = $client->post('temperature-logs/eq-123', ['temperature' => 3.5]);

    expect($result->successful())->toBeTrue();
    expect($result->status())->toBe(201);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://test.appcc.digital/api/v1/temperature-logs/eq-123'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request['temperature'] === 3.5;
    });
});

it('sends PATCH requests with correct auth and url', function () {
    Http::fake([
        'https://test.appcc.digital/api/v1/blast-chiller-logs/log-123' => Http::response(['data' => ['id' => 'log-123']], 200),
    ]);

    $client = new AppccClient(
        url: 'https://test.appcc.digital',
        token: 'my-token',
        timeout: 10,
    );

    $result = $client->patch('blast-chiller-logs/log-123', ['mid_temp' => 30.0]);

    expect($result->successful())->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->method() === 'PATCH'
            && $request->hasHeader('Authorization', 'Bearer my-token')
            && $request['mid_temp'] === 30.0;
    });
});

it('handles 422 validation errors', function () {
    Http::fake([
        'https://test.appcc.digital/api/v1/cleaning-logs' => Http::response([
            'message' => 'Validation failed',
            'errors' => ['zone_id' => ['The zone_id field is required.']],
        ], 422),
    ]);

    $client = new AppccClient(
        url: 'https://test.appcc.digital',
        token: 'test-token',
        timeout: 10,
    );

    $result = $client->post('cleaning-logs', []);

    expect($result->failed())->toBeTrue();
    expect($result->status())->toBe(422);
    expect($result->errors())->toHaveKey('zone_id');
});

it('handles 401 unauthorized errors', function () {
    Http::fake([
        'https://test.appcc.digital/api/v1/cleaning-logs' => Http::response([
            'message' => 'Token inválido.',
        ], 401),
    ]);

    $client = new AppccClient(
        url: 'https://test.appcc.digital',
        token: 'bad-token',
        timeout: 10,
    );

    $result = $client->post('cleaning-logs', ['zone_id' => 'z']);

    expect($result->failed())->toBeTrue();
    expect($result->message())->toBe('Token inválido.');
});

it('sends multipart requests when photo is a file path', function () {
    Http::fake([
        'https://test.appcc.digital/api/v1/temperature-logs/eq-1' => Http::response(['data' => ['id' => '1']], 201),
    ]);

    $tmpFile = tempnam(sys_get_temp_dir(), 'test_photo');
    file_put_contents($tmpFile, 'fake image content');

    $client = new AppccClient(
        url: 'https://test.appcc.digital',
        token: 'test-token',
        timeout: 10,
    );

    $result = $client->post('temperature-logs/eq-1', ['temperature' => 3.5], $tmpFile);

    expect($result->successful())->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->header('Content-Type')[0] ?? '', 'multipart');
    });

    unlink($tmpFile);
});
