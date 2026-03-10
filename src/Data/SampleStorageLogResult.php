<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class SampleStorageLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public ?string $recordedBy,
        public string $sampleDate,
        public string $productName,
        public int $quantityGrams,
        public float $storageTemp,
        public ?string $containerType,
        public ?string $destructionDate,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            recordedBy: $data['recorded_by'] ?? null,
            sampleDate: $data['sample_date'],
            productName: $data['product_name'],
            quantityGrams: (int) $data['quantity_grams'],
            storageTemp: (float) $data['storage_temp'],
            containerType: $data['container_type'] ?? null,
            destructionDate: $data['destruction_date'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
