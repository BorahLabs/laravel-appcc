<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\VegetableDisinfectionLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class VegetableDisinfectionLogResource
{
    private ?array $pendingData = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    /**
     * @return AppccResult<VegetableDisinfectionLogResult>
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
            'vegetable-disinfection-logs',
            $this->pendingData ?? [],
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }

    /**
     * @return AppccResult<VegetableDisinfectionLogResult>
     */
    private function send(): AppccResult
    {
        $result = $this->client->post('vegetable-disinfection-logs', $this->pendingData ?? []);

        return $result->withDto(VegetableDisinfectionLogResult::class);
    }
}
