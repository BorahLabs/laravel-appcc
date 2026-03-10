<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class VegetableDisinfectionLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public ?string $recordedBy,
        public string $disinfectionDate,
        public string $productName,
        public string $startTime,
        public string $endTime,
        public int $chlorineConcentrationPpm,
        public ?string $observations,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            recordedBy: $data['recorded_by'] ?? null,
            disinfectionDate: $data['disinfection_date'],
            productName: $data['product_name'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            chlorineConcentrationPpm: (int) $data['chlorine_concentration_ppm'],
            observations: $data['observations'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
