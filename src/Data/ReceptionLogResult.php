<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class ReceptionLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $supplierId,
        public ?string $recordedBy,
        public string $receptionDate,
        public ?string $deliveryNoteNumber,
        public string $productName,
        public ?string $lotNumber,
        public ?string $expiryDate,
        public ?float $temperature,
        public string $visualEvaluation,
        public bool $packagingOk,
        public bool $labelingOk,
        public bool $allergensOk,
        public ?string $rejectionReason,
        public ?string $correctiveAction,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            supplierId: $data['supplier_id'],
            recordedBy: $data['recorded_by'] ?? null,
            receptionDate: $data['reception_date'],
            deliveryNoteNumber: $data['delivery_note_number'] ?? null,
            productName: $data['product_name'],
            lotNumber: $data['lot_number'] ?? null,
            expiryDate: $data['expiry_date'] ?? null,
            temperature: isset($data['temperature']) ? (float) $data['temperature'] : null,
            visualEvaluation: $data['visual_evaluation'],
            packagingOk: (bool) ($data['packaging_ok'] ?? true),
            labelingOk: (bool) ($data['labeling_ok'] ?? true),
            allergensOk: (bool) ($data['allergens_ok'] ?? true),
            rejectionReason: $data['rejection_reason'] ?? null,
            correctiveAction: $data['corrective_action'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
