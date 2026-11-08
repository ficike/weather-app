<?php

namespace ADS\TakeHome;

include __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/helpers.php";

use ADS\TakeHome\Controllers\ExampleController;
use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\Config\Config;
use ADS\TakeHome\Framework\Database\MySQL;
use ADS\TakeHome\Framework\Http\HttpKernel;
use ADS\TakeHome\Framework\Http\HttpExceptionResponseFactory;
use ADS\TakeHome\Framework\Http\HttpResponseEmitter;
use ADS\TakeHome\Framework\Http\Router;
use ADS\TakeHome\Repositories\WeatherRepository;
use ADS\TakeHome\Services\Weather\WeatherReportFactory;
use ADS\TakeHome\Services\Weather\WeatherService;
use ADS\TakeHome\Services\Weather\WeatherSettings;
use ADS\TakeHome\Services\Weather\WeatherStatisticsCalculator;
use Throwable;

$debug = getenv('APP_DEBUG') === 'true';
$responseEmitter = new HttpResponseEmitter();
$exceptionResponseFactory = new HttpExceptionResponseFactory($debug);
set_exception_handler(static function (Throwable $exception) use ($responseEmitter, $exceptionResponseFactory): void {
    $responseEmitter->emit($exceptionResponseFactory->create($exception));
});

$router = new Router();
$config = Config::load(__DIR__ . '/../config');
$databaseConfig = $config->require('database.' . $config->require('database.driver'));
$mysql = new MySQL(
    $databaseConfig['host'],
    $databaseConfig['schema'],
    $databaseConfig['username'],
    $databaseConfig['password'],
    $databaseConfig['port'],
);
$exampleController = new ExampleController(greeting: 'Hello', db: $mysql);
$weatherService = new WeatherService(
    new WeatherRepository($mysql),
    new WeatherReportFactory(new WeatherStatisticsCalculator()),
);
$weatherController = new WeatherController(
    $weatherService,
    new WeatherSettings(
        $config->require('weather.units'),
        $config->require('weather.decimal_places'),
    ),
);

$router->addRoute('GET', '/hello', $exampleController->sayHello(...));
$router->addRoute('GET', '/budapest', $exampleController->getBudapestMaxTempCount(...));
$router->addRoute('GET', '/weather-config', $weatherController->getWeatherConfig(...));
$router->addRoute('GET', '/cities', $weatherController->getCities(...));
$router->addRoute('GET', '/regions', $weatherController->getRegions(...));
$router->addRoute('GET', '/city-weather', $weatherController->getCityWeather(...));
$router->addRoute('GET', '/region-weather', $weatherController->getRegionWeather(...));

(new HttpKernel($router, $responseEmitter, $debug))->handleRequest();
