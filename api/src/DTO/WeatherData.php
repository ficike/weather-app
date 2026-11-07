<?php

declare(strict_types=1);

namespace ADS\TakeHome\DTO;

use DateTime;
use JsonSerializable;

class WeatherData implements JsonSerializable
{
    public DateTime $date;
    public ?float $minTemp;
    public ?float $maxTemp;
    public ?float $precipitation;

    public function __construct(array $data)
    {
        $this->date = DateTime::createFromFormat(config('date.input_format'), (string) $data['date']);
        $this->minTemp = $data['min_temp'] === null ? null : (float) $data['min_temp'];
        $this->maxTemp = $data['max_temp'] === null ? null : (float) $data['max_temp'];
        $this->precipitation = $data['precipitation'] === null ? null : (float) $data['precipitation'];
    }

    /** @return array{date: string, min_temp: float|null, max_temp: float|null, precipitation: float|null} */
    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date->format(config('date.output_format')),
            'min_temp' => $this->minTemp,
            'max_temp' => $this->maxTemp,
            'precipitation' => $this->precipitation,
        ];
    }
}
