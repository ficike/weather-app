<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

use ADS\TakeHome\DTO\Request\CityWeatherQuery;
use DateTimeImmutable;

/** Supplies the validation rules for the weather endpoint. */
class CityWeatherRequestValidator extends WeatherRequestValidator
{
    public function __construct()
    {
        parent::__construct([
            'cityId' => ['required', 'integer', 'min:1'],
        ]);
    }

    /** @param array<string, string|list<string>> $data */
    public function validateWeatherQuery(array $data): CityWeatherQuery
    {
        $validated = $this->validatedData($data);

        // The rules above guarantee these types on a successful validation.
        /** @var int $cityId */
        $cityId = $validated['cityId'];
        /** @var DateTimeImmutable $startDate */
        $startDate = $validated['startDate'];
        /** @var DateTimeImmutable $endDate */
        $endDate = $validated['endDate'];

        return new CityWeatherQuery($cityId, $startDate, $endDate);
    }
}
