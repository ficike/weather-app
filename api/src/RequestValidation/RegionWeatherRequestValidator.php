<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

use ADS\TakeHome\DTO\Request\RegionWeatherQuery;
use DateTimeImmutable;

/** Supplies the validation rules for the weather endpoint. */
class RegionWeatherRequestValidator extends WeatherRequestValidator
{
    public function __construct()
    {
        parent::__construct([
            'region' => ['required', 'string'],
        ]);
    }

    /** @param array<string, string|list<string>> $data */
    public function validateWeatherQuery(array $data): RegionWeatherQuery
    {
        $validated = $this->validatedData($data);

        // The rules above guarantee these types on a successful validation.
        /** @var string $region */
        $region = $validated['region'];
        /** @var DateTimeImmutable $startDate */
        $startDate = $validated['startDate'];
        /** @var DateTimeImmutable $endDate */
        $endDate = $validated['endDate'];

        return new RegionWeatherQuery($region, $startDate, $endDate);
    }
}
