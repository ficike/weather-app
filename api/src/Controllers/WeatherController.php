<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use ADS\TakeHome\RequestValidation\RegionWeatherRequestValidator;
use ADS\TakeHome\Framework\Http\HttpRequest;
use ADS\TakeHome\Framework\Http\HttpResponseInterface;
use ADS\TakeHome\Framework\Http\JsonHttpResponse;
use ADS\TakeHome\RequestValidation\CityWeatherRequestValidator;
use ADS\TakeHome\Services\Weather\WeatherService;
use ADS\TakeHome\Services\Weather\WeatherSettings;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherService $weatherService,
        private readonly WeatherSettings $weatherSettings,
    )
    {
    }

    public function getWeatherConfig(HttpRequest $request): HttpResponseInterface
    {
        return new JsonHttpResponse([
            'units' => $this->weatherSettings->units,
            'decimals' => $this->weatherSettings->decimalPlaces,
        ]);
    }

    public function getCities(HttpRequest $request): HttpResponseInterface
    {
        return new JsonHttpResponse($this->weatherService->findCities());
    }

    public function getRegions(HttpRequest $request): HttpResponseInterface
    {
        return new JsonHttpResponse($this->weatherService->findRegions());
    }

    public function getCityWeather(HttpRequest $request): HttpResponseInterface
    {
        $validated = $this->validateRequest($request, new CityWeatherRequestValidator());
        $weather = $this->weatherService->findCityWeather(...$validated);
        return new JsonHttpResponse($weather);
    }

    public function getRegionWeather(HttpRequest $request): HttpResponseInterface
    {
        $validated = $this->validateRequest($request, new RegionWeatherRequestValidator());
        $weather = $this->weatherService->findRegionWeather(...$validated);
        return new JsonHttpResponse($weather);
    }
}
