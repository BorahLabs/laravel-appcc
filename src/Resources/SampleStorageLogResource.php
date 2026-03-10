<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\SampleStorageLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class SampleStorageLogResource
{
    private ?array $pendingData = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    /**
     * @return AppccResult<SampleStorageLogResult>
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
            'sample-storage-logs',
            $this->pendingData ?? [],
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }

    /**
     * @return AppccResult<SampleStorageLogResult>
     */
    private function send(): AppccResult
    {
        $result = $this->client->post('sample-storage-logs', $this->pendingData ?? []);

        return $result->withDto(SampleStorageLogResult::class);
    }
}
