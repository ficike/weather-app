<?php

declare(strict_types=1);

namespace ADS\TakeHome\Services\Weather;

use ADS\TakeHome\DTO\WeatherData;
use ADS\TakeHome\Repositories\WeatherRepositoryInterface;
use DateTimeInterface;

/** Coordinates weather queries and prepares their API reports. */
class WeatherService
{
    public function __construct(
        private readonly WeatherRepositoryInterface $repository,
        private readonly WeatherReportFactory $reportFactory,
    ) {
    }

    /** @return array<int, string> */
    public function findCities(): array
    {
        return $this->repository->findCities();
    }

    /** @return array<string, string> */
    public function findRegions(): array
    {
        return $this->repository->findRegions();
    }

    /** @return array{header: array<string, string>, data: list<WeatherData>, statistics: array<string, array{median: float|null, mode: list<float>}>} */
    public function findCityWeather(int $cityId, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        return $this->reportFactory->create(
            $this->repository->findCityWeatherRows($cityId, $startDate, $endDate),
            ['date' => 'Dátum', 'min_temp' => 'Minimum hőmérséklet', 'max_temp' => 'Maximum hőmérséklet', 'precipitation' => 'Csapadék'],
        );
    }

    /** @return array{header: array<string, string>, data: list<WeatherData>, statistics: array<string, array{median: float|null, mode: list<float>}>} */
    public function findRegionWeather(string $region, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        return $this->reportFactory->create(
            $this->repository->findRegionWeatherRows($region, $startDate, $endDate),
            ['date' => 'Dátum', 'min_temp' => 'Átlag minimum hőmérséklet', 'max_temp' => 'Átlag maximum hőmérséklet', 'precipitation' => 'Átlag csapadék'],
        );
    }
}
