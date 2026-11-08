<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Framework\Database\DatabaseConnectionInterface;
use DateTimeInterface;

class WeatherRepository implements WeatherRepositoryInterface
{
    public function __construct(private DatabaseConnectionInterface $db) {}

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
     * @return list<array<string, int|float|string|null>>
     */
    public function findCityWeatherRows(
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

        return $rows;
    }

    /**
     * @param string $region
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return list<array<string, int|float|string|null>>
     */
    public function findRegionWeatherRows(
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

        return $rows;
    }

}
