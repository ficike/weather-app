<?php

declare(strict_types=1);

namespace ADS\TakeHome\Services\Weather;

use ADS\TakeHome\DTO\WeatherData;

/** Builds the API representation of weather query rows. */
class WeatherReportFactory
{
    public function __construct(private readonly WeatherStatisticsCalculator $statisticsCalculator)
    {
    }

    /**
     * @param list<array<string, int|float|string|null>> $rows
     * @param array<string, string> $header
     * @return array{header: array<string, string>, data: list<WeatherData>, statistics: array<string, array{median: float|null, mode: list<float>}>}
     */
    public function create(array $rows, array $header): array
    {
        return [
            'header' => $header,
            'data' => array_map(static fn (array $row): WeatherData => WeatherData::fromDatabaseRow($row), $rows),
            'statistics' => $this->statisticsCalculator->calculate($rows),
        ];
    }
}
