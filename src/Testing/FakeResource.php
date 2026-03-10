<?php

namespace AppccDigital\LaravelAppcc\Testing;

use AppccDigital\LaravelAppcc\Results\AppccResult;

class FakeResource
{
    private ?string $resourceId = null;

    public function __construct(
        private readonly AppccFake $fake,
        private readonly string $endpoint,
        private readonly string $resourceClass,
    ) {}

    public function for(string $id): static
    {
        $this->resourceId = $id;

        return $this;
    }

    public function record(array $data, mixed $photo = null): AppccResult
    {
        $fullEndpoint = $this->resourceId
            ? "{$this->endpoint}/{$this->resourceId}"
            : $this->endpoint;

        return $this->fake->recordRequest($this->endpoint, $fullEndpoint, 'post', $data, $photo);
    }

    public function update(array $data): AppccResult
    {
        $fullEndpoint = $this->resourceId
            ? "{$this->endpoint}/{$this->resourceId}"
            : $this->endpoint;

        return $this->fake->recordRequest($this->endpoint, $fullEndpoint, 'patch', $data);
    }

    public function dispatch(?string $queue = null): void
    {
        // No-op in fake mode
    }
}
