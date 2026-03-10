<?php

namespace AppccDigital\LaravelAppcc\Testing;

use AppccDigital\LaravelAppcc\Resources\BlastChillerLogResource;
use AppccDigital\LaravelAppcc\Resources\CleaningLogResource;
use AppccDigital\LaravelAppcc\Resources\IncidentLogResource;
use AppccDigital\LaravelAppcc\Resources\ReceptionLogResource;
use AppccDigital\LaravelAppcc\Resources\SampleStorageLogResource;
use AppccDigital\LaravelAppcc\Resources\TemperatureLogResource;
use AppccDigital\LaravelAppcc\Resources\VacuumPackagingLogResource;
use AppccDigital\LaravelAppcc\Resources\VegetableDisinfectionLogResource;
use AppccDigital\LaravelAppcc\Resources\WaterControlLogResource;
use AppccDigital\LaravelAppcc\Results\AppccResult;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Response as ClientResponse;
use PHPUnit\Framework\Assert;

class AppccFake
{
    /** @var array<string, list<array{method: string, endpoint: string, data: array, photo: mixed}>> */
    private array $recorded = [];

    /** @var array<string, AppccFakeResponse> */
    private array $responses;

    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    public function temperatureLogs(): FakeResource
    {
        return new FakeResource($this, 'temperature-logs', TemperatureLogResource::class);
    }

    public function cleaningLogs(): FakeResource
    {
        return new FakeResource($this, 'cleaning-logs', CleaningLogResource::class);
    }

    public function incidentLogs(): FakeResource
    {
        return new FakeResource($this, 'incident-logs', IncidentLogResource::class);
    }

    public function vacuumPackagingLogs(): FakeResource
    {
        return new FakeResource($this, 'vacuum-packaging-logs', VacuumPackagingLogResource::class);
    }

    public function receptionLogs(): FakeResource
    {
        return new FakeResource($this, 'reception-logs', ReceptionLogResource::class);
    }

    public function blastChillerLogs(): FakeResource
    {
        return new FakeResource($this, 'blast-chiller-logs', BlastChillerLogResource::class);
    }

    public function waterControlLogs(): FakeResource
    {
        return new FakeResource($this, 'water-control-logs', WaterControlLogResource::class);
    }

    public function vegetableDisinfectionLogs(): FakeResource
    {
        return new FakeResource($this, 'vegetable-disinfection-logs', VegetableDisinfectionLogResource::class);
    }

    public function sampleStorageLogs(): FakeResource
    {
        return new FakeResource($this, 'sample-storage-logs', SampleStorageLogResource::class);
    }

    public function recordRequest(string $baseEndpoint, string $fullEndpoint, string $method, array $data, mixed $photo = null): AppccResult
    {
        $this->recorded[$baseEndpoint][] = [
            'method' => $method,
            'endpoint' => $fullEndpoint,
            'data' => $data,
            'photo' => $photo,
        ];

        $fakeResponse = $this->responses[$baseEndpoint] ?? null;

        if ($fakeResponse instanceof AppccFakeResponse) {
            $body = json_encode(['data' => $fakeResponse->data]);
            $psrResponse = new Response($fakeResponse->status, ['Content-Type' => 'application/json'], $body);
        } else {
            $body = json_encode(['data' => array_merge(['id' => 'fake-id'], $data)]);
            $psrResponse = new Response(201, ['Content-Type' => 'application/json'], $body);
        }

        return new AppccResult(new ClientResponse($psrResponse));
    }

    public function assertSent(string $endpoint, ?int $count = null): void
    {
        $recorded = $this->recorded[$endpoint] ?? [];

        if ($count !== null) {
            Assert::assertCount(
                $count,
                $recorded,
                "Expected {$count} requests to [{$endpoint}], got ".count($recorded),
            );
        } else {
            Assert::assertNotEmpty(
                $recorded,
                "Expected at least one request to [{$endpoint}], got none",
            );
        }
    }

    public function assertSentWith(string $endpoint, array $data): void
    {
        $recorded = $this->recorded[$endpoint] ?? [];

        Assert::assertNotEmpty($recorded, "No requests recorded for [{$endpoint}]");

        $found = false;

        foreach ($recorded as $request) {
            if ($this->arrayContains($request['data'], $data)) {
                $found = true;

                break;
            }
        }

        Assert::assertTrue(
            $found,
            "No request to [{$endpoint}] contained the expected data: ".json_encode($data),
        );
    }

    public function assertNotSent(string $endpoint): void
    {
        $recorded = $this->recorded[$endpoint] ?? [];

        Assert::assertEmpty(
            $recorded,
            "Expected no requests to [{$endpoint}], got ".count($recorded),
        );
    }

    public function assertNothingSent(): void
    {
        $total = array_sum(array_map('count', $this->recorded));

        Assert::assertSame(
            0,
            $total,
            "Expected no requests, got {$total}",
        );
    }

    public function assertNothingSentTo(string $endpoint): void
    {
        $this->assertNotSent($endpoint);
    }

    /**
     * @return array<int, array{method: string, endpoint: string, data: array, photo: mixed}>
     */
    public function recorded(?string $endpoint = null): array
    {
        if ($endpoint !== null) {
            return $this->recorded[$endpoint] ?? [];
        }

        return array_merge(...array_values($this->recorded ?: [[]]));
    }

    private function arrayContains(array $haystack, array $needle): bool
    {
        foreach ($needle as $key => $value) {
            if (! array_key_exists($key, $haystack) || $haystack[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}
