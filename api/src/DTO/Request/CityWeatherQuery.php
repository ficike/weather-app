<?php

declare(strict_types=1);

namespace ADS\TakeHome\DTO\Request;

use DateTimeImmutable;

final class CityWeatherQuery
{
    public function __construct(
        public readonly int $cityId,
        public readonly DateTimeImmutable $startDate,
        public readonly DateTimeImmutable $endDate,
    ) {
    }
}
