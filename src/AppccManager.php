<?php

namespace AppccDigital\LaravelAppcc;

use AppccDigital\LaravelAppcc\Http\AppccClient;
use AppccDigital\LaravelAppcc\Resources\BlastChillerLogResource;
use AppccDigital\LaravelAppcc\Resources\CleaningLogResource;
use AppccDigital\LaravelAppcc\Resources\IncidentLogResource;
use AppccDigital\LaravelAppcc\Resources\ReceptionLogResource;
use AppccDigital\LaravelAppcc\Resources\SampleStorageLogResource;
use AppccDigital\LaravelAppcc\Resources\TemperatureLogResource;
use AppccDigital\LaravelAppcc\Resources\VacuumPackagingLogResource;
use AppccDigital\LaravelAppcc\Resources\VegetableDisinfectionLogResource;
use AppccDigital\LaravelAppcc\Resources\WaterControlLogResource;

class AppccManager
{
    public function __construct(
        private readonly AppccClient $client,
    ) {}

    public function temperatureLogs(): TemperatureLogResource
    {
        return new TemperatureLogResource($this->client);
    }

    public function cleaningLogs(): CleaningLogResource
    {
        return new CleaningLogResource($this->client);
    }

    public function incidentLogs(): IncidentLogResource
    {
        return new IncidentLogResource($this->client);
    }

    public function vacuumPackagingLogs(): VacuumPackagingLogResource
    {
        return new VacuumPackagingLogResource($this->client);
    }

    public function receptionLogs(): ReceptionLogResource
    {
        return new ReceptionLogResource($this->client);
    }

    public function blastChillerLogs(): BlastChillerLogResource
    {
        return new BlastChillerLogResource($this->client);
    }

    public function waterControlLogs(): WaterControlLogResource
    {
        return new WaterControlLogResource($this->client);
    }

    public function vegetableDisinfectionLogs(): VegetableDisinfectionLogResource
    {
        return new VegetableDisinfectionLogResource($this->client);
    }

    public function sampleStorageLogs(): SampleStorageLogResource
    {
        return new SampleStorageLogResource($this->client);
    }

    public function client(): AppccClient
    {
        return $this->client;
    }
}
