<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests;

use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\Config;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Framework\JsonHttpResponse;
use ADS\TakeHome\Repositories\WeatherRepository;
use ADS\TakeHome\RequestValidation\RequestValidationException;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WeatherControllerTest extends TestCase
{
    protected function setUp(): void
    {
        Config::load(__DIR__ . '/../config');
    }

    #[Test]
    public function weatherConfigReturnsUnitsAndDecimalPlaces(): void
    {
        $controller = new WeatherController($this->createMock(WeatherRepository::class));

        $response = $controller->getWeatherConfig(new HttpRequest([]));

        self::assertInstanceOf(JsonHttpResponse::class, $response);
        self::assertSame(200, $response->getStatus());
        self::assertSame([
            'units' => config('weather.units'),
            'decimals' => config('weather.decimal_places'),
        ], json_decode($response->getBody(), true));
    }

    #[Test]
    public function citiesEndpointReturnsRepositoryCities(): void
    {
        $repository = $this->createMock(WeatherRepository::class);
        $repository->expects(self::once())->method('findCities')->willReturn([1 => 'Budapest (Közép-Magyarország)']);
        $controller = new WeatherController($repository);

        $response = $controller->getCities(new HttpRequest([]));

        self::assertSame(200, $response->getStatus());
        self::assertSame(['1' => 'Budapest (Közép-Magyarország)'], json_decode($response->getBody(), true));
    }

    #[Test]
    public function regionsEndpointReturnsRepositoryRegions(): void
    {
        $repository = $this->createMock(WeatherRepository::class);
        $repository->expects(self::once())->method('findRegions')->willReturn(['Közép-Magyarország' => 'Közép-Magyarország']);
        $controller = new WeatherController($repository);

        $response = $controller->getRegions(new HttpRequest([]));

        self::assertSame(200, $response->getStatus());
        self::assertSame(['Közép-Magyarország' => 'Közép-Magyarország'], json_decode($response->getBody(), true));
    }

    #[Test]
    public function cityWeatherPassesValidatedParametersToRepository(): void
    {
        $weather = ['header' => ['date' => 'Dátum'], 'data' => [], 'statistics' => []];
        $repository = $this->createMock(WeatherRepository::class);
        $repository->expects(self::once())
            ->method('findByCityAndDateRange')
            ->with(1, new DateTimeImmutable('2024-01-01'), new DateTimeImmutable('2024-01-02'))
            ->willReturn($weather);
        $controller = new WeatherController($repository);

        $response = $controller->getCityWeather(new HttpRequest([
            'cityId' => '1', 'startDate' => '20240101', 'endDate' => '20240102',
        ]));

        self::assertSame(200, $response->getStatus());
        self::assertSame($weather, json_decode($response->getBody(), true));
    }

    #[Test]
    public function cityWeatherRejectsInvalidRequest(): void
    {
        $repository = $this->createMock(WeatherRepository::class);
        $repository->expects(self::never())->method('findByCityAndDateRange');
        $controller = new WeatherController($repository);

        try {
            $controller->getCityWeather(new HttpRequest(['cityId' => '0', 'startDate' => '20240102', 'endDate' => '20240101']));
            self::fail('Expected request validation to fail.');
        } catch (RequestValidationException $exception) {
            self::assertArrayHasKey('cityId', $exception->errors);
            self::assertArrayHasKey('endDate', $exception->errors);
        }
    }

    #[Test]
    public function regionWeatherPassesValidatedParametersToRepository(): void
    {
        $weather = ['header' => ['date' => 'Dátum'], 'data' => [], 'statistics' => []];
        $repository = $this->createMock(WeatherRepository::class);
        $repository->expects(self::once())
            ->method('findByRegionAndDateRange')
            ->with('Közép-Magyarország', new DateTimeImmutable('2024-01-01'), new DateTimeImmutable('2024-01-02'))
            ->willReturn($weather);
        $controller = new WeatherController($repository);

        $response = $controller->getRegionWeather(new HttpRequest([
            'region' => 'Közép-Magyarország', 'startDate' => '20240101', 'endDate' => '20240102',
        ]));

        self::assertSame(200, $response->getStatus());
        self::assertSame($weather, json_decode($response->getBody(), true));
    }

    #[Test]
    public function regionWeatherRejectsMissingRegion(): void
    {
        $repository = $this->createMock(WeatherRepository::class);
        $repository->expects(self::never())->method('findByRegionAndDateRange');
        $controller = new WeatherController($repository);

        try {
            $controller->getRegionWeather(new HttpRequest(['startDate' => '20240101', 'endDate' => '20240102']));
            self::fail('Expected request validation to fail.');
        } catch (RequestValidationException $exception) {
            self::assertArrayHasKey('region', $exception->errors);
        }
    }
}
