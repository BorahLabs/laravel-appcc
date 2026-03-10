<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class VacuumPackagingLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public ?string $recordedBy,
        public ?string $productId,
        public string $packagingDate,
        public ?string $lotReference,
        public float $productTempBefore,
        public bool $sealIntegrityOk,
        public bool $labeledCorrectly,
        public ?string $expiryDate,
        public ?string $observations,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            recordedBy: $data['recorded_by'] ?? null,
            productId: $data['product_id'] ?? null,
            packagingDate: $data['packaging_date'],
            lotReference: $data['lot_reference'] ?? null,
            productTempBefore: (float) $data['product_temp_before'],
            sealIntegrityOk: (bool) $data['seal_integrity_ok'],
            labeledCorrectly: (bool) $data['labeled_correctly'],
            expiryDate: $data['expiry_date'] ?? null,
            observations: $data['observations'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
