<?php

namespace AppccDigital\LaravelAppcc\Facades;

use AppccDigital\LaravelAppcc\AppccManager;
use AppccDigital\LaravelAppcc\Resources\BlastChillerLogResource;
use AppccDigital\LaravelAppcc\Resources\CleaningLogResource;
use AppccDigital\LaravelAppcc\Resources\IncidentLogResource;
use AppccDigital\LaravelAppcc\Resources\ReceptionLogResource;
use AppccDigital\LaravelAppcc\Resources\SampleStorageLogResource;
use AppccDigital\LaravelAppcc\Resources\TemperatureLogResource;
use AppccDigital\LaravelAppcc\Resources\VacuumPackagingLogResource;
use AppccDigital\LaravelAppcc\Resources\VegetableDisinfectionLogResource;
use AppccDigital\LaravelAppcc\Resources\WaterControlLogResource;
use AppccDigital\LaravelAppcc\Testing\AppccFake;
use AppccDigital\LaravelAppcc\Testing\AppccFakeResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static TemperatureLogResource temperatureLogs()
 * @method static CleaningLogResource cleaningLogs()
 * @method static IncidentLogResource incidentLogs()
 * @method static VacuumPackagingLogResource vacuumPackagingLogs()
 * @method static ReceptionLogResource receptionLogs()
 * @method static BlastChillerLogResource blastChillerLogs()
 * @method static WaterControlLogResource waterControlLogs()
 * @method static VegetableDisinfectionLogResource vegetableDisinfectionLogs()
 * @method static SampleStorageLogResource sampleStorageLogs()
 * @method static void assertSent(string $endpoint, ?int $count = null)
 * @method static void assertSentWith(string $endpoint, array $data)
 * @method static void assertNotSent(string $endpoint)
 * @method static void assertNothingSent()
 * @method static void assertNothingSentTo(string $endpoint)
 * @method static array recorded(?string $endpoint = null)
 *
 * @see \AppccDigital\LaravelAppcc\AppccManager
 * @see \AppccDigital\LaravelAppcc\Testing\AppccFake
 */
class Appcc extends Facade
{
    public static function fake(array $responses = []): void
    {
        $fake = new AppccFake($responses);

        static::swap($fake);
    }

    public static function response(array $data, int $status = 200): AppccFakeResponse
    {
        return new AppccFakeResponse($data, $status);
    }

    protected static function getFacadeAccessor(): string
    {
        return AppccManager::class;
    }
}
