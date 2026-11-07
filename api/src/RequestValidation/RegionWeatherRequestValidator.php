<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

/** Supplies the validation rules for the weather endpoint. */
class RegionWeatherRequestValidator extends RequestValidator
{
    public function __construct()
    {
        parent::__construct([
            'region' => ['required'],
            'startDate' => ['required', 'date_format:Ymd'],
            'endDate' => ['required', 'date_format:Ymd', 'after_or_equal:startDate'],
        ]);
    }
}
