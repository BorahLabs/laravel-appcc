<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\VacuumPackagingLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class VacuumPackagingLogResource
{
    private ?array $pendingData = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    /**
     * @return AppccResult<VacuumPackagingLogResult>
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
            'vacuum-packaging-logs',
            $this->pendingData ?? [],
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }

    /**
     * @return AppccResult<VacuumPackagingLogResult>
     */
    private function send(): AppccResult
    {
        $result = $this->client->post('vacuum-packaging-logs', $this->pendingData ?? []);

        return $result->withDto(VacuumPackagingLogResult::class);
    }
}
