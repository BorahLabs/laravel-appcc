<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class BlastChillerLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public ?string $recordedBy,
        public ?string $productId,
        public ?string $equipmentId,
        public ?string $lotReference,
        public string $status,
        public string $entryTime,
        public float $entryTemp,
        public ?string $midTime,
        public ?float $midTemp,
        public ?string $exitTime,
        public ?float $exitTemp,
        public ?bool $isWithinLimits,
        public ?string $correctiveAction,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            recordedBy: $data['recorded_by'] ?? null,
            productId: $data['product_id'] ?? null,
            equipmentId: $data['equipment_id'] ?? null,
            lotReference: $data['lot_reference'] ?? null,
            status: $data['status'],
            entryTime: $data['entry_time'],
            entryTemp: (float) $data['entry_temp'],
            midTime: $data['mid_time'] ?? null,
            midTemp: isset($data['mid_temp']) ? (float) $data['mid_temp'] : null,
            exitTime: $data['exit_time'] ?? null,
            exitTemp: isset($data['exit_temp']) ? (float) $data['exit_temp'] : null,
            isWithinLimits: isset($data['is_within_limits']) ? (bool) $data['is_within_limits'] : null,
            correctiveAction: $data['corrective_action'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
