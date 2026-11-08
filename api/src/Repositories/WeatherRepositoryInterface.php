<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use DateTimeInterface;

interface WeatherRepositoryInterface
{
    /** @return array<int, string> */
    public function findCities(): array;

    /** @return array<string, string> */
    public function findRegions(): array;

    /** @return list<array<string, int|float|string|null>> */
    public function findCityWeatherRows(
        int $cityId,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
    ): array;

    /** @return list<array<string, int|float|string|null>> */
    public function findRegionWeatherRows(
        string $region,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
    ): array;
}
