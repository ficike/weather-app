<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use ADS\TakeHome\RequestValidation\RegionWeatherRequestValidator;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\JsonHttpResponse;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\RequestValidation\CityWeatherRequestValidator;
use ADS\TakeHome\Repositories\WeatherRepository;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherRepository $weatherRepository)
    {
    }

    public function getCities(HttpRequest $request): HttpResponseInterface
    {
        return new JsonHttpResponse($this->weatherRepository->findCities());
    }

    public function getRegions(HttpRequest $request): HttpResponseInterface
    {
        return new JsonHttpResponse($this->weatherRepository->findRegions());
    }

    public function getCityWeather(HttpRequest $request): HttpResponseInterface
    {
        $validated = $this->validateRequest($request, new CityWeatherRequestValidator());
        $weather = $this->weatherRepository->findByCityAndDateRange(...$validated);
        return new JsonHttpResponse($weather);
    }

    public function getRegionWeather(HttpRequest $request): HttpResponseInterface
    {
        $validated = $this->validateRequest($request, new RegionWeatherRequestValidator());
        $weather = $this->weatherRepository->findByRegionAndDateRange(...$validated);
        return new JsonHttpResponse($weather);
    }
}
