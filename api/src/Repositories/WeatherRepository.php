<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Framework\MySQL;
use ADS\TakeHome\DTO\WeatherData;
use DateTimeInterface;

class WeatherRepository
{
    public function __construct(private MySQL $db) {}

    /** @return array<int, string> City IDs mapped to city names. */
    public function findCities(): array
    {
        $rows = $this->db->queryAssoc(
            "SELECT id, CONCAT(name, ' (', region, ')') AS name FROM cities ORDER BY name"
        );

        $cities = [];
        foreach ($rows as $row) {
            $cities[$row['id']] = $row['name'];
        }
        return $cities;
    }

    /** @return array<string, string> Region names mapped to themselves. */
    public function findRegions(): array
    {
        $rows = $this->db->queryAssoc('SELECT DISTINCT region FROM cities ORDER BY region');

        $regions = [];
        foreach ($rows as $row) {
            $regions[$row['region']] = $row['region'];
        }
        return $regions;
    }

    /**
     * @return array{
     *   header: array<string, string>,
     *   data: list<WeatherData>,
     *   statistics: array<string, array{median: float|null, mode: list<float>}>
     * }
     */
    public function findByCityAndDateRange(
        int $cityId,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
    ): array
    {
        $rows = $this->db->queryAssoc(
            'SELECT mx.date, mn.min_temp, mx.max_temp, p.precipitation
             FROM maximum_temperatures AS mx
             LEFT JOIN minimum_temperatures AS mn
               ON mn.city_id = mx.city_id AND mn.date = mx.date
             LEFT JOIN precipitation AS p
               ON p.city_id = mx.city_id AND p.date = mx.date
             WHERE mx.city_id = :cityId
               AND mx.date BETWEEN :startDate AND :endDate
             ORDER BY mx.date',
            [
                ':cityId' => $cityId,
                ':startDate' => (int)$startDate->format('Ymd'),
                ':endDate' => (int)$endDate->format('Ymd'),
            ],
        );

        return [
            'header' => [
                'date' => 'Dátum',
                'min_temp' => 'Minimum hőmérséklet',
                'max_temp' => 'Maximum hőmérséklet',
                'precipitation' => 'Csapadék',
            ],
            'data' => $this->formatWeatherData($rows),
            'statistics' => $this->summarizeWeatherData($rows),
        ];
    }

    /**
     * @param string $region
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return array{
     *   header: array<string, string>,
     *   data: list<WeatherData>,
     *   statistics: array<string, array{median: float|null, mode: list<float>}>
     * }
     */
    public function findByRegionAndDateRange(
        string $region,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
    ): array
    {
        $rows = $this->db->queryAssoc(
            'SELECT mx.date,
                 AVG(mn.min_temp) AS min_temp,
                 AVG(mx.max_temp) AS max_temp,
                 AVG(p.precipitation) AS precipitation
                 FROM maximum_temperatures AS mx
                 LEFT JOIN minimum_temperatures AS mn
                     ON mn.city_id = mx.city_id AND mn.date = mx.date
                 LEFT JOIN precipitation AS p
                     ON p.city_id = mx.city_id AND p.date = mx.date
                 WHERE mx.city_id IN (SELECT id FROM cities WHERE region = :region)
                   AND mx.date BETWEEN :startDate AND :endDate
                 GROUP BY mx.date
                 ORDER BY mx.date',
            [
                ':region' => $region,
                ':startDate' => (int)$startDate->format('Ymd'),
                ':endDate' => (int)$endDate->format('Ymd'),
            ],
        );

        return [
            'header' => [
                'date' => 'Dátum',
                'min_temp' => 'Átlag minimum hőmérséklet',
                'max_temp' => 'Átlag maximum hőmérséklet',
                'precipitation' => 'Átlag csapadék',
            ],
            'data' => $this->formatWeatherData($rows),
            'statistics' => $this->summarizeWeatherData($rows),
        ];
    }

    /**
     * @param list<array<string, int|float|string|null>> $rows
     * @return list<WeatherData>
     */
    public function formatWeatherData(array $rows): array
    {
        $weather = [];
        foreach ($rows as $row) {
            $weather[] = new WeatherData($row);
        }
        return $weather;
    }

    /**
     * @param list<array<string, int|float|string|null>> $rows
     * @return array<string, array{median: float|null, mode: list<float>}>
     */
    private function summarizeWeatherData(array $rows): array
    {
        $summary = [];
        foreach (['min_temp', 'max_temp'] as $column) {
            $values = array_values(array_filter(
                array_map(static fn (array $row): ?float => $row[$column] === null ? null : (float)$row[$column], $rows),
                static fn (?float $value): bool => $value !== null,
            ));
            sort($values, SORT_NUMERIC);

            if ($values === []) {
                $summary[$column] = ['median' => null, 'mode' => []];
                continue;
            }

            $count = count($values);
            $middle = intdiv($count, 2);
            $median = $count % 2 === 1
                ? $values[$middle]
                : ($values[$middle - 1] + $values[$middle]) / 2;

            $frequencies = array_count_values(array_map(static fn (float $value): string => (string)$value, $values));
            $maxFrequency = max($frequencies);
            $modes = [];
            if ($maxFrequency > 1) {
                foreach ($frequencies as $value => $frequency) {
                    if ($frequency === $maxFrequency) {
                        $modes[] = (float)$value;
                    }
                }
            }

            $summary[$column] = [
                'median' => $median,
                'mode' => $modes,
            ];
        }

        return $summary;
    }

}
