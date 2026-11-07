<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Framework\MySQL;
use ADS\TakeHome\DTO\WeatherData;
use DateTimeInterface;

readonly class WeatherRepository
{
    public function __construct(private MySQL $db) {}

    /** @return list<WeatherData> */
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

        return $this->formatWeatherData($rows);
    }

    /**
     * @param string $region
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return list<WeatherData>
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

        return $this->formatWeatherData($rows);
    }

    /**
     * @param array $rows
     * @return array
     */
    public function formatWeatherData(array $rows): array
    {
        $weather = [];
        foreach ($rows as $row) {
            $weather[] = new WeatherData($row);
        }
        return $weather;
    }
}
