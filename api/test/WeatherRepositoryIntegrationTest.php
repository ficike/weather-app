<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests;

use ADS\TakeHome\Framework\Config;
use ADS\TakeHome\Repositories\WeatherRepository;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;

class WeatherRepositoryIntegrationTest extends DatabaseIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::load(__DIR__ . '/../config');
        $this->seedWeatherData();
    }

    protected function tearDown(): void
    {
        $this->cleanWeatherData();
        parent::tearDown();
    }

    #[Test]
    public function findsCitiesAndRegionsFromDatabase(): void
    {
        $repository = new WeatherRepository($this->db);

        self::assertSame('Integrációs tesztváros (Dunántúl)', $repository->findCities()[987654321]);
        self::assertSame('Dunántúl', $repository->findRegions()['Dunántúl']);
    }

    #[Test]
    public function findsCityWeatherAndCalculatesStatistics(): void
    {
        $repository = new WeatherRepository($this->db);

        $result = $repository->findByCityAndDateRange(
            987654321,
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-01-02'),
        );

        self::assertSame('Minimum hőmérséklet', $result['header']['min_temp']);
        self::assertCount(2, $result['data']);
        self::assertSame([
            'date' => '2024-01-01', 'min_temp' => 0.0, 'max_temp' => 10.0, 'precipitation' => 2.0,
        ], $result['data'][0]->jsonSerialize());
        self::assertSame(['median' => 2.0, 'mode' => []], $result['statistics']['min_temp']);
        self::assertSame(['median' => 15.0, 'mode' => []], $result['statistics']['max_temp']);
    }

    #[Test]
    public function findsRegionalAverageWeatherFromDatabase(): void
    {
        $repository = new WeatherRepository($this->db);

        $result = $repository->findByRegionAndDateRange(
            'Dunántúl',
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-01-02'),
        );

        self::assertSame('Átlag minimum hőmérséklet', $result['header']['min_temp']);
        self::assertCount(2, $result['data']);
        self::assertSame([
            'date' => '2024-01-02', 'min_temp' => 4.0, 'max_temp' => 20.0, 'precipitation' => 6.0,
        ], $result['data'][1]->jsonSerialize());
    }
}
