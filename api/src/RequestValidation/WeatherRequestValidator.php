<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

use ADS\TakeHome\RequestValidation\Core\RequestValidationException;
use ADS\TakeHome\RequestValidation\Core\RequestValidator;
use DateTimeImmutable;

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

    /**
     * @param array<string, string|list<string>> $data
     * @return array<string, string|list<string>|int|DateTimeImmutable>
     */
    protected function validatedData(array $data): array
    {
        $result = $this->validate($data);
        if ($result['validated'] === null) {
            throw new RequestValidationException($result['errors']);
        }

        return $result['validated'];
    }
}
