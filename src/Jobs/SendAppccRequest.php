<?php

namespace AppccDigital\LaravelAppcc\Jobs;

use AppccDigital\LaravelAppcc\AppccManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppccRequest implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $method,
        public readonly string $endpoint,
        public readonly array $data,
        public readonly mixed $photo = null,
    ) {}

    public function handle(AppccManager $manager): void
    {
        $client = $manager->client();

        match ($this->method) {
            'post' => $client->post($this->endpoint, $this->data, $this->photo),
            'patch' => $client->patch($this->endpoint, $this->data),
            default => $client->post($this->endpoint, $this->data),
        };
    }
}
