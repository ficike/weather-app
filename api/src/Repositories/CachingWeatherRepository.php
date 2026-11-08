<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Framework\Cache\CacheInterface;
use DateTimeInterface;
use Throwable;

final class CachingWeatherRepository implements WeatherRepositoryInterface
{
    private const CACHE_TTL_SECONDS = 60;

    public function __construct(
        private readonly WeatherRepositoryInterface $repository,
        private readonly CacheInterface $cache,
    ) {
    }

    public function findCities(): array
    {
        return $this->repository->findCities();
    }

    public function findRegions(): array
    {
        return $this->repository->findRegions();
    }

    public function findCityWeatherRows(
        int $cityId,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
    ): array {
        $key = sprintf('weather:city:%d:%s:%s', $cityId, $startDate->format('Ymd'), $endDate->format('Ymd'));

        return $this->rememberRows($key, fn () => $this->repository->findCityWeatherRows(
            $cityId,
            $startDate,
            $endDate,
        ));
    }

    public function findRegionWeatherRows(
        string $region,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
    ): array {
        $key = sprintf(
            'weather:region:%s:%s:%s',
            hash('sha256', $region),
            $startDate->format('Ymd'),
            $endDate->format('Ymd'),
        );

        return $this->rememberRows($key, fn () => $this->repository->findRegionWeatherRows(
            $region,
            $startDate,
            $endDate,
        ));
    }

    /**
     * @param callable(): list<array<string, int|float|string|null>> $load
     * @return list<array<string, int|float|string|null>>
     */
    private function rememberRows(string $key, callable $load): array
    {
        try {
            $cached = $this->cache->get($key);
        } catch (Throwable $exception) {
            error_log('Weather cache read failed: ' . $exception->getMessage());
            $cached = null;
        }
        if (self::isWeatherRows($cached)) {
            return $cached;
        }

        $rows = $load();
        try {
            $this->cache->set($key, $rows, self::CACHE_TTL_SECONDS);
        } catch (Throwable $exception) {
            error_log('Weather cache write failed: ' . $exception->getMessage());
        }

        return $rows;
    }

    /** @phpstan-assert-if-true list<array<string, int|float|string|null>> $rows */
    private static function isWeatherRows(mixed $rows): bool
    {
        if (!is_array($rows) || !array_is_list($rows)) {
            return false;
        }

        foreach ($rows as $row) {
            if (!is_array($row)) {
                return false;
            }

            foreach ($row as $key => $value) {
                if (!is_string($key) || (!is_int($value) && !is_float($value) && !is_string($value) && $value !== null)) {
                    return false;
                }
            }
        }

        return true;
    }
}
