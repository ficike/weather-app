<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

use ADS\TakeHome\RequestValidation\Core\RequestValidator;

/** Base validator for weather requests with a shared date range. */
abstract class WeatherRequestValidator extends RequestValidator
{
    /**
     * @param array<string, list<string>> $additionalRules
     */
    protected function __construct(array $additionalRules)
    {
        parent::__construct($additionalRules + [
            'startDate' => ['required', 'date_format:Ymd'],
            'endDate' => ['required', 'date_format:Ymd', 'after_or_equal:startDate'],
        ]);
    }
}
