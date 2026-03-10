<?php

namespace AppccDigital\LaravelAppcc\Data;

readonly class IncidentLogResult
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public ?string $recordedBy,
        public string $incidentDate,
        public string $elementInvolved,
        public string $incidentDescription,
        public string $correctiveAction,
        public ?string $resolutionDate,
        public ?string $observations,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            tenantId: $data['tenant_id'],
            recordedBy: $data['recorded_by'] ?? null,
            incidentDate: $data['incident_date'],
            elementInvolved: $data['element_involved'],
            incidentDescription: $data['incident_description'],
            correctiveAction: $data['corrective_action'],
            resolutionDate: $data['resolution_date'] ?? null,
            observations: $data['observations'] ?? null,
            createdAt: $data['created_at'],
        );
    }
}
