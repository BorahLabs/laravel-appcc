<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class WaterControlLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public ?string $recordedBy,
        public string $sampleDate,
        public string $sampleLocation,
        public float $chlorineLevel,
        public float $phLevel,
        public bool $isChlorineOk,
        public bool $isPhOk,
        public ?string $correctiveAction,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            recordedBy: $data['recorded_by'] ?? null,
            sampleDate: $data['sample_date'],
            sampleLocation: $data['sample_location'],
            chlorineLevel: (float) $data['chlorine_level'],
            phLevel: (float) $data['ph_level'],
            isChlorineOk: (bool) $data['is_chlorine_ok'],
            isPhOk: (bool) $data['is_ph_ok'],
            correctiveAction: $data['corrective_action'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
