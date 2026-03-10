<?php

namespace AppccDigital\LaravelAppcc\Resources;

use AppccDigital\LaravelAppcc\Data\ReceptionLogResult;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Jobs\SendAppccRequest;
use AppccDigital\LaravelAppcc\Results\AppccResult;

class ReceptionLogResource
{
    private ?array $pendingData = null;

    private mixed $pendingPhoto = null;

    public function __construct(
        private readonly AppccClient $client,
    ) {}

    /**
     * @return AppccResult<ReceptionLogResult>
     */
    public function record(array $data, mixed $photo = null): AppccResult
    {
        $this->pendingData = $data;
        $this->pendingPhoto = $photo;

        return $this->send();
    }

    public function dispatch(?string $queue = null): void
    {
        SendAppccRequest::dispatch(
            'post',
            'reception-logs',
            $this->pendingData ?? [],
            $this->pendingPhoto,
        )->onQueue($queue ?? config('appcc.queue', 'default'));
    }

    /**
     * @return AppccResult<ReceptionLogResult>
     */
    private function send(): AppccResult
    {
        $result = $this->client->post(
            'reception-logs',
            $this->pendingData ?? [],
            $this->pendingPhoto,
        );

        return $result->withDto(ReceptionLogResult::class);
    }
}
