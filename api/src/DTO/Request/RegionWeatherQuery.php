<?php

declare(strict_types=1);

namespace ADS\TakeHome\DTO\Request;

use DateTimeImmutable;

final class RegionWeatherQuery
{
    public function __construct(
        public readonly string $region,
        public readonly DateTimeImmutable $startDate,
        public readonly DateTimeImmutable $endDate,
    ) {
    }
}
