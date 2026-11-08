<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

/** Supplies the validation rules for the weather endpoint. */
class CityWeatherRequestValidator extends WeatherRequestValidator
{
    public function __construct()
    {
        parent::__construct([
            'cityId' => ['required', 'integer', 'min:1'],
        ]);
    }
}
