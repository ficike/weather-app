<?php

declare(strict_types=1);

namespace ADS\TakeHome\Services\Weather;

/** Weather settings exposed by the API. */
final class WeatherSettings
{
    /** @param array<string, array{label: string, temperature: string, precipitation: string}> $units */
    public function __construct(
        public readonly array $units,
        public readonly int $decimalPlaces,
    ) {
    }
}
