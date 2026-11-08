<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests;

use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\Http\HttpRequest;
use ADS\TakeHome\Framework\Http\JsonHttpResponse;
use ADS\TakeHome\Services\Weather\WeatherService;
use ADS\TakeHome\Services\Weather\WeatherSettings;
use ADS\TakeHome\RequestValidation\Core\RequestValidationException;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WeatherControllerTest extends TestCase
{
    #[Test]
    public function weatherConfigReturnsUnitsAndDecimalPlaces(): void
    {
        $controller = $this->controller($this->createMock(WeatherService::class));

        $response = $controller->getWeatherConfig(new HttpRequest([]));

        self::assertInstanceOf(JsonHttpResponse::class, $response);
        self::assertSame(200, $response->getStatus());
        self::assertSame([
            'units' => ['si' => ['label' => 'SI', 'temperature' => '°C', 'precipitation' => 'mm']],
            'decimals' => 1,
        ], json_decode($response->getBody(), true));
    }

    #[Test]
    public function citiesEndpointReturnsRepositoryCities(): void
    {
        $service = $this->createMock(WeatherService::class);
        $service->expects(self::once())->method('findCities')->willReturn([1 => 'Budapest (Közép-Magyarország)']);
        $controller = $this->controller($service);

        $response = $controller->getCities(new HttpRequest([]));

        self::assertSame(200, $response->getStatus());
        self::assertSame(['1' => 'Budapest (Közép-Magyarország)'], json_decode($response->getBody(), true));
    }

    #[Test]
    public function regionsEndpointReturnsRepositoryRegions(): void
    {
        $service = $this->createMock(WeatherService::class);
        $service->expects(self::once())->method('findRegions')->willReturn(['Közép-Magyarország' => 'Közép-Magyarország']);
        $controller = $this->controller($service);

        $response = $controller->getRegions(new HttpRequest([]));

        self::assertSame(200, $response->getStatus());
        self::assertSame(['Közép-Magyarország' => 'Közép-Magyarország'], json_decode($response->getBody(), true));
    }

    #[Test]
    public function cityWeatherPassesValidatedParametersToRepository(): void
    {
        $weather = ['header' => ['date' => 'Dátum'], 'data' => [], 'statistics' => []];
        $service = $this->createMock(WeatherService::class);
        $service->expects(self::once())
            ->method('findCityWeather')
            ->with(1, new DateTimeImmutable('2024-01-01'), new DateTimeImmutable('2024-01-02'))
            ->willReturn($weather);
        $controller = $this->controller($service);

        $response = $controller->getCityWeather(new HttpRequest([
            'cityId' => '1', 'startDate' => '20240101', 'endDate' => '20240102',
        ]));

        self::assertSame(200, $response->getStatus());
        self::assertSame($weather, json_decode($response->getBody(), true));
    }

    #[Test]
    public function cityWeatherRejectsInvalidRequest(): void
    {
        $service = $this->createMock(WeatherService::class);
        $service->expects(self::never())->method('findCityWeather');
        $controller = $this->controller($service);

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
        $service = $this->createMock(WeatherService::class);
        $service->expects(self::once())
            ->method('findRegionWeather')
            ->with('Közép-Magyarország', new DateTimeImmutable('2024-01-01'), new DateTimeImmutable('2024-01-02'))
            ->willReturn($weather);
        $controller = $this->controller($service);

        $response = $controller->getRegionWeather(new HttpRequest([
            'region' => 'Közép-Magyarország', 'startDate' => '20240101', 'endDate' => '20240102',
        ]));

        self::assertSame(200, $response->getStatus());
        self::assertSame($weather, json_decode($response->getBody(), true));
    }

    #[Test]
    public function regionWeatherRejectsMissingRegion(): void
    {
        $service = $this->createMock(WeatherService::class);
        $service->expects(self::never())->method('findRegionWeather');
        $controller = $this->controller($service);

        try {
            $controller->getRegionWeather(new HttpRequest(['startDate' => '20240101', 'endDate' => '20240102']));
            self::fail('Expected request validation to fail.');
        } catch (RequestValidationException $exception) {
            self::assertArrayHasKey('region', $exception->errors);
        }
    }

    private function controller(WeatherService $service): WeatherController
    {
        return new WeatherController($service, new WeatherSettings(
            ['si' => ['label' => 'SI', 'temperature' => '°C', 'precipitation' => 'mm']],
            1,
        ));
    }
}
