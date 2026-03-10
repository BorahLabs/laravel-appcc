<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\IncidentLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class IncidentLogResource
{
    private ?array $pendingData = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    /**
     * @return AppccResult<IncidentLogResult>
     */
    public function record(array $data): AppccResult
    {
        $this->pendingData = $data;

        return $this->send();
    }

    public function dispatch(?string $queue = null): void
    {
        SendAppccRequest::dispatch(
            'post',
            'incident-logs',
            $this->pendingData ?? [],
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }

    /**
     * @return AppccResult<IncidentLogResult>
     */
    private function send(): AppccResult
    {
        $result = $this->client->post('incident-logs', $this->pendingData ?? []);

        return $result->withDto(IncidentLogResult::class);
    }
}
