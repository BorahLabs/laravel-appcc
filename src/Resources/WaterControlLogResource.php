<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\WaterControlLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class WaterControlLogResource
{
    private ?array $pendingData = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    /**
     * @return AppccResult<WaterControlLogResult>
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
            'water-control-logs',
            $this->pendingData ?? [],
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }

    /**
     * @return AppccResult<WaterControlLogResult>
     */
    private function send(): AppccResult
    {
        $result = $this->client->post('water-control-logs', $this->pendingData ?? []);

        return $result->withDto(WaterControlLogResult::class);
    }
}
