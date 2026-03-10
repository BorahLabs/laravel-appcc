<?php

use AppccDigital\LaravelAppcc\Data\BlastChillerLogResult;
use AppccDigital\LaravelAppcc\Data\CleaningLogResult;
use AppccDigital\LaravelAppcc\Data\IncidentLogResult;
use AppccDigital\LaravelAppcc\Data\ReceptionLogResult;
use AppccDigital\LaravelAppcc\Data\SampleStorageLogResult;
use AppccDigital\LaravelAppcc\Data\TemperatureLogResult;
use AppccDigital\LaravelAppcc\Data\VacuumPackagingLogResult;
use AppccDigital\LaravelAppcc\Data\VegetableDisinfectionLogResult;
use AppccDigital\LaravelAppcc\Data\WaterControlLogResult;

it('maps temperature log result from array', function () {
    $dto = TemperatureLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'equipment_id' => '01E',
        'recorded_by' => 'user-1',
        'temperature' => 4.2,
        'is_within_limits' => false,
        'corrective_action' => 'Adjusted',
        'recorded_at' => '2026-03-10T10:00:00Z',
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->id)->toBe('01H');
    expect($dto->temperature)->toBe(4.2);
    expect($dto->isWithinLimits)->toBeFalse();
    expect($dto->recordedBy)->toBe('user-1');
    expect($dto->correctiveAction)->toBe('Adjusted');
});

it('maps cleaning log result from array', function () {
    $dto = CleaningLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'zone_id' => '01Z',
        'recorded_by' => null,
        'type' => 'daily',
        'cleaning_date' => '2026-03-10',
        'evaluation' => 'correct',
        'items' => null,
        'observations' => 'Todo limpio',
        'corrective_action' => null,
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->type)->toBe('daily');
    expect($dto->evaluation)->toBe('correct');
    expect($dto->observations)->toBe('Todo limpio');
});

it('maps incident log result from array', function () {
    $dto = IncidentLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'recorded_by' => null,
        'incident_date' => '2026-03-10',
        'element_involved' => 'Horno',
        'incident_description' => 'Fallo eléctrico',
        'corrective_action' => 'Reparado',
        'resolution_date' => '2026-03-11',
        'observations' => null,
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->elementInvolved)->toBe('Horno');
    expect($dto->incidentDescription)->toBe('Fallo eléctrico');
    expect($dto->resolutionDate)->toBe('2026-03-11');
});

it('maps vacuum packaging log result from array', function () {
    $dto = VacuumPackagingLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'recorded_by' => null,
        'product_id' => '01P',
        'packaging_date' => '2026-03-10',
        'lot_reference' => 'LOT-001',
        'product_temp_before' => 4.0,
        'seal_integrity_ok' => true,
        'labeled_correctly' => true,
        'expiry_date' => '2026-03-20',
        'observations' => null,
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->productTempBefore)->toBe(4.0);
    expect($dto->sealIntegrityOk)->toBeTrue();
    expect($dto->expiryDate)->toBe('2026-03-20');
});

it('maps reception log result from array', function () {
    $dto = ReceptionLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'supplier_id' => '01S',
        'recorded_by' => null,
        'reception_date' => '2026-03-10',
        'delivery_note_number' => 'ALB-123',
        'product_name' => 'Pollo',
        'lot_number' => 'L-789',
        'expiry_date' => '2026-04-10',
        'temperature' => 2.5,
        'visual_evaluation' => 'ok',
        'packaging_ok' => true,
        'labeling_ok' => true,
        'allergens_ok' => true,
        'rejection_reason' => null,
        'corrective_action' => null,
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->productName)->toBe('Pollo');
    expect($dto->temperature)->toBe(2.5);
    expect($dto->visualEvaluation)->toBe('ok');
    expect($dto->packagingOk)->toBeTrue();
});

it('maps blast chiller log result from array', function () {
    $dto = BlastChillerLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'recorded_by' => null,
        'product_id' => null,
        'equipment_id' => '01E',
        'lot_reference' => null,
        'status' => 'entry',
        'entry_time' => '2026-03-10T10:00:00Z',
        'entry_temp' => 65.0,
        'mid_time' => null,
        'mid_temp' => null,
        'exit_time' => null,
        'exit_temp' => null,
        'is_within_limits' => null,
        'corrective_action' => null,
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->status)->toBe('entry');
    expect($dto->entryTemp)->toBe(65.0);
    expect($dto->midTemp)->toBeNull();
    expect($dto->exitTemp)->toBeNull();
});

it('maps water control log result from array', function () {
    $dto = WaterControlLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'recorded_by' => null,
        'sample_date' => '2026-03-10',
        'sample_location' => 'Cocina',
        'chlorine_level' => 0.5,
        'ph_level' => 7.2,
        'is_chlorine_ok' => true,
        'is_ph_ok' => true,
        'corrective_action' => null,
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->chlorineLevel)->toBe(0.5);
    expect($dto->phLevel)->toBe(7.2);
    expect($dto->isChlorineOk)->toBeTrue();
    expect($dto->isPhOk)->toBeTrue();
});

it('maps vegetable disinfection log result from array', function () {
    $dto = VegetableDisinfectionLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'recorded_by' => null,
        'disinfection_date' => '2026-03-10',
        'product_name' => 'Lechuga',
        'start_time' => '10:00',
        'end_time' => '10:15',
        'chlorine_concentration_ppm' => 100,
        'observations' => null,
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->productName)->toBe('Lechuga');
    expect($dto->chlorineConcentrationPpm)->toBe(100);
    expect($dto->startTime)->toBe('10:00');
});

it('maps sample storage log result from array', function () {
    $dto = SampleStorageLogResult::fromArray([
        'id' => '01H',
        'tenant_id' => '01T',
        'recorded_by' => null,
        'sample_date' => '2026-03-10',
        'product_name' => 'Paella',
        'quantity_grams' => 150,
        'storage_temp' => -18.0,
        'container_type' => 'Bolsa hermética',
        'destruction_date' => '2026-03-17',
        'created_at' => '2026-03-10T10:00:00Z',
    ]);

    expect($dto->productName)->toBe('Paella');
    expect($dto->quantityGrams)->toBe(150);
    expect($dto->storageTemp)->toBe(-18.0);
    expect($dto->destructionDate)->toBe('2026-03-17');
});
