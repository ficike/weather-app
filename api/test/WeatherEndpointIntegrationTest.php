<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests;

use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\Http\HttpKernel;
use ADS\TakeHome\Framework\Http\HttpResponseEmitter;
use ADS\TakeHome\Framework\Http\Router;
use ADS\TakeHome\Repositories\WeatherRepository;
use ADS\TakeHome\Services\Weather\WeatherReportFactory;
use ADS\TakeHome\Services\Weather\WeatherService;
use ADS\TakeHome\Services\Weather\WeatherSettings;
use ADS\TakeHome\Services\Weather\WeatherStatisticsCalculator;
use PHPUnit\Framework\Attributes\Test;

class WeatherEndpointIntegrationTest extends DatabaseIntegrationTestCase
{
    private int|false $previousStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedWeatherData();
        $this->previousStatus = http_response_code();
    }

    protected function tearDown(): void
    {
        $this->cleanWeatherData();
        if ($this->previousStatus !== false) {
            http_response_code($this->previousStatus);
        }
        parent::tearDown();
    }

    #[Test]
    public function cityWeatherEndpointReturnsDatabaseResults(): void
    {
        $response = $this->dispatch('/city-weather', [
            'cityId' => '987654321', 'startDate' => '20240101', 'endDate' => '20240102',
        ]);

        self::assertSame(200, $response['status']);
        self::assertCount(2, $response['body']['data']);
        self::assertSame('2024-01-01', $response['body']['data'][0]['date']);
        self::assertSame(15, $response['body']['statistics']['max_temp']['median']);
    }

    #[Test]
    public function cityWeatherEndpointReturnsBadRequestForInvalidInput(): void
    {
        $response = $this->dispatch('/city-weather', [
            'cityId' => '0', 'startDate' => '20240102', 'endDate' => '20240101',
        ]);

        self::assertSame(400, $response['status']);
        self::assertArrayHasKey('cityId', $response['body']['errors']);
        self::assertArrayHasKey('endDate', $response['body']['errors']);
    }

    #[Test]
    public function regionWeatherEndpointReturnsDatabaseResults(): void
    {
        $response = $this->dispatch('/region-weather', [
            'region' => 'Dunántúl', 'startDate' => '20240101', 'endDate' => '20240102',
        ]);

        self::assertSame(200, $response['status']);
        self::assertCount(2, $response['body']['data']);
        self::assertSame(15, $response['body']['statistics']['max_temp']['median']);
        self::assertSame('Átlag maximum hőmérséklet', $response['body']['header']['max_temp']);
    }

    #[Test]
    public function regionWeatherEndpointReturnsBadRequestForMissingRegion(): void
    {
        $response = $this->dispatch('/region-weather', [
            'startDate' => '20240101', 'endDate' => '20240102',
        ]);

        self::assertSame(400, $response['status']);
        self::assertArrayHasKey('region', $response['body']['errors']);
    }

    /** @param array<string, string> $query @return array{status: int, body: array} */
    private function dispatch(string $path, array $query): array
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $path;
        $_GET = $query;

        $router = new Router();
        $controller = new WeatherController(new WeatherService(
            new WeatherRepository($this->db),
            new WeatherReportFactory(new WeatherStatisticsCalculator()),
        ), new WeatherSettings([], 1));
        $router->addRoute('GET', '/city-weather', $controller->getCityWeather(...));
        $router->addRoute('GET', '/region-weather', $controller->getRegionWeather(...));

        ob_start();
        try {
            (new HttpKernel($router, new HttpResponseEmitter()))->handleRequest();
            $output = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        $body = json_decode($output === false ? '' : $output, true);
        self::assertIsArray($body, 'The endpoint should return a JSON object.');

        return ['status' => http_response_code(), 'body' => $body];
    }
}
