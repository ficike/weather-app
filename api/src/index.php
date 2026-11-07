<?php

namespace ADS\TakeHome;

include __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/helpers.php";

use ADS\TakeHome\Controllers\ExampleController;
use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\MySQL;
use ADS\TakeHome\Framework\Router;
use ADS\TakeHome\Framework\Config;
use ADS\TakeHome\Repositories\WeatherRepository;

$router = new Router();
$dbConfig = Config::load(__DIR__ . "/../config")->get('database.mysql');
$mysql = new MySQL(...$dbConfig);
$helloController = new ExampleController(greeting: "Hello", db: $mysql);
$weatherController = new WeatherController(new WeatherRepository($mysql));

$router->addRoute("GET", "/hello", $helloController->sayHello(...));
$router->addRoute("GET", "/budapest", $helloController->getBudapestMaxTempCount(...));

// TODO: többi végpont
$router->addRoute("GET", "/weather-config", $weatherController->getWeatherConfig(...));
$router->addRoute("GET", "/cities", $weatherController->getCities(...));
$router->addRoute("GET", "/regions", $weatherController->getRegions(...));
$router->addRoute("GET", "/city-weather", $weatherController->getCityWeather(...));
$router->addRoute("GET", "/region-weather", $weatherController->getRegionWeather(...));

$router->handleRequest();
