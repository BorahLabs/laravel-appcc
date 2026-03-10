<?php

use AppccDigital\LaravelAppcc\Facades\Appcc;

beforeEach(function () {
    Appcc::fake();
});

it('records temperature log requests', function () {
    Appcc::temperatureLogs()->for('equipment-123')->record([
        'temperature' => 3.5,
    ]);

    Appcc::assertSent('temperature-logs', 1);
    Appcc::assertSentWith('temperature-logs', [
        'temperature' => 3.5,
    ]);
});

it('records cleaning log requests', function () {
    Appcc::cleaningLogs()->record([
        'zone_id' => 'zone-123',
        'type' => 'daily',
        'evaluation' => 'correct',
    ]);

    Appcc::assertSent('cleaning-logs', 1);
    Appcc::assertSentWith('cleaning-logs', [
        'zone_id' => 'zone-123',
        'type' => 'daily',
    ]);
});

it('records incident log requests', function () {
    Appcc::incidentLogs()->record([
        'element_involved' => 'Horno',
        'incident_description' => 'Fallo eléctrico',
        'corrective_action' => 'Reparado',
    ]);

    Appcc::assertSent('incident-logs', 1);
});

it('records vacuum packaging log requests', function () {
    Appcc::vacuumPackagingLogs()->record([
        'product_temp_before' => 4.0,
        'seal_integrity_ok' => true,
        'labeled_correctly' => true,
    ]);

    Appcc::assertSent('vacuum-packaging-logs', 1);
});

it('records reception log requests', function () {
    Appcc::receptionLogs()->record([
        'supplier_id' => 'supplier-123',
        'product_name' => 'Pollo fresco',
        'visual_evaluation' => 'ok',
    ]);

    Appcc::assertSent('reception-logs', 1);
});

it('records blast chiller log requests', function () {
    Appcc::blastChillerLogs()->record([
        'entry_temp' => 65.0,
    ]);

    Appcc::assertSent('blast-chiller-logs', 1);
});

it('records blast chiller update requests', function () {
    Appcc::blastChillerLogs()->for('log-123')->update([
        'mid_temp' => 30.0,
    ]);

    Appcc::assertSent('blast-chiller-logs', 1);
});

it('records water control log requests', function () {
    Appcc::waterControlLogs()->record([
        'sample_location' => 'Cocina',
        'chlorine_level' => 0.5,
        'ph_level' => 7.2,
    ]);

    Appcc::assertSent('water-control-logs', 1);
});

it('records vegetable disinfection log requests', function () {
    Appcc::vegetableDisinfectionLogs()->record([
        'product_name' => 'Lechuga',
        'start_time' => '10:00',
        'end_time' => '10:15',
        'chlorine_concentration_ppm' => 100,
    ]);

    Appcc::assertSent('vegetable-disinfection-logs', 1);
});

it('records sample storage log requests', function () {
    Appcc::sampleStorageLogs()->record([
        'product_name' => 'Paella',
        'quantity_grams' => 150,
        'storage_temp' => -18.0,
    ]);

    Appcc::assertSent('sample-storage-logs', 1);
});

it('asserts nothing sent', function () {
    Appcc::assertNothingSent();
});

it('asserts not sent to specific endpoint', function () {
    Appcc::cleaningLogs()->record([
        'zone_id' => 'zone-123',
        'type' => 'daily',
        'evaluation' => 'correct',
    ]);

    Appcc::assertNotSent('temperature-logs');
    Appcc::assertNothingSentTo('incident-logs');
});

it('returns custom fake responses', function () {
    Appcc::fake([
        'temperature-logs' => Appcc::response([
            'id' => 'custom-id',
            'temperature' => 3.5,
            'is_within_limits' => true,
        ], 201),
    ]);

    $result = Appcc::temperatureLogs()->for('eq-123')->record([
        'temperature' => 3.5,
    ]);

    expect($result->successful())->toBeTrue();
    expect($result->status())->toBe(201);
    expect($result->toArray()['data']['id'])->toBe('custom-id');
});

it('returns fake validation error responses', function () {
    Appcc::fake([
        'incident-logs' => Appcc::response([
            'message' => 'Validation failed',
            'errors' => ['element_involved' => ['Required']],
        ], 422),
    ]);

    $result = Appcc::incidentLogs()->record([]);

    expect($result->failed())->toBeTrue();
    expect($result->status())->toBe(422);
});

it('can retrieve all recorded requests', function () {
    Appcc::temperatureLogs()->for('eq-1')->record(['temperature' => 1.0]);
    Appcc::temperatureLogs()->for('eq-2')->record(['temperature' => 2.0]);
    Appcc::cleaningLogs()->record(['zone_id' => 'z-1', 'type' => 'daily', 'evaluation' => 'correct']);

    $all = Appcc::recorded();
    expect($all)->toHaveCount(3);

    $tempOnly = Appcc::recorded('temperature-logs');
    expect($tempOnly)->toHaveCount(2);
});

it('counts multiple requests correctly', function () {
    Appcc::temperatureLogs()->for('eq-1')->record(['temperature' => 1.0]);
    Appcc::temperatureLogs()->for('eq-2')->record(['temperature' => 2.0]);

    Appcc::assertSent('temperature-logs', 2);
});
