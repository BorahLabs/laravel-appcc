<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\BlastChillerLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class BlastChillerLogResource
{
    private ?string $logId = null;

    private ?array $pendingData = null;

    private ?string $pendingMethod = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    public function for(string $logId): static
    {
        $this->logId = $logId;

        return $this;
    }

    /**
     * @return AppccResult<BlastChillerLogResult>
     */
    public function record(array $data): AppccResult
    {
        $this->pendingData = $data;
        $this->pendingMethod = 'post';

        $result = $this->client->post('blast-chiller-logs', $data);

        return $result->withDto(BlastChillerLogResult::class);
    }

    /**
     * @return AppccResult<BlastChillerLogResult>
     */
    public function update(array $data): AppccResult
    {
        $this->pendingData = $data;
        $this->pendingMethod = 'patch';

        $result = $this->client->patch("blast-chiller-logs/{$this->logId}", $data);

        return $result->withDto(BlastChillerLogResult::class);
    }

    public function dispatch(?string $queue = null): void
    {
        $method = $this->pendingMethod ?? 'post';
        $endpoint = $method === 'patch'
            ? "blast-chiller-logs/{$this->logId}"
            : 'blast-chiller-logs';

        SendAppccRequest::dispatch(
            $method,
            $endpoint,
            $this->pendingData ?? [],
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }
}
