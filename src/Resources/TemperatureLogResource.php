<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\TemperatureLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class TemperatureLogResource
{
    private ?string $equipmentId = null;

    private ?array $pendingData = null;

    private mixed $pendingPhoto = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    public function for(string $equipmentId): static
    {
        $this->equipmentId = $equipmentId;

        return $this;
    }

    /**
     * @return AppccResult<TemperatureLogResult>
     */
    public function record(array $data, mixed $photo = null): static|AppccResult
    {
        $this->pendingData = $data;
        $this->pendingPhoto = $photo;

        return $this->send();
    }

    public function dispatch(?string $queue = null): void
    {
        SendAppccRequest::dispatch(
            'post',
            "temperature-logs/{$this->equipmentId}",
            $this->pendingData ?? [],
            $this->pendingPhoto,
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }

    /**
     * @return AppccResult<TemperatureLogResult>
     */
    private function send(): AppccResult
    {
        $result = $this->client->post(
            "temperature-logs/{$this->equipmentId}",
            $this->pendingData ?? [],
            $this->pendingPhoto,
        );

        return $result->withDto(TemperatureLogResult::class);
    }
}
