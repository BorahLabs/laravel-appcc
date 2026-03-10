<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class TemperatureLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $equipmentId,
        public ?string $recordedBy,
        public float $temperature,
        public bool $isWithinLimits,
        public ?string $correctiveAction,
        public string $recordedAt,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            equipmentId: $data['equipment_id'],
            recordedBy: $data['recorded_by'] ?? null,
            temperature: (float) $data['temperature'],
            isWithinLimits: (bool) $data['is_within_limits'],
            correctiveAction: $data['corrective_action'] ?? null,
            recordedAt: $data['recorded_at'],
            createdAt: $data['created_at'],
        );
    }
}
