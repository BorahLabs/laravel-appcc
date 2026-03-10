<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class CleaningLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $zoneId,
        public ?string $recordedBy,
        public string $type,
        public string $cleaningDate,
        public string $evaluation,
        public ?array $items,
        public ?string $observations,
        public ?string $correctiveAction,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            zoneId: $data['zone_id'],
            recordedBy: $data['recorded_by'] ?? null,
            type: $data['type'],
            cleaningDate: $data['cleaning_date'],
            evaluation: $data['evaluation'],
            items: $data['items'] ?? null,
            observations: $data['observations'] ?? null,
            correctiveAction: $data['corrective_action'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
