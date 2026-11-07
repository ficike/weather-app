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

    /** @return array{date: string, min_temp: string|null, max_temp: string|null, precipitation: string|null} */
    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date->format(config('date.output_format')),
            'min_temp' => $this->formatUnit($this->minTemp, config('weather.temperature.unit')),
            'max_temp' => $this->formatUnit($this->maxTemp, config('weather.temperature.unit')),
            'precipitation' => $this->formatUnit($this->precipitation, config('weather.precipitation.unit')),
        ];
    }

    private function formatUnit(?float $value, string $unit): ?string
    {
        if ($value === null) {
            return null;
        }

        return number_format(
            $value,
            config('weather.temperature.decimals'),
            config('weather.temperature.decimal_separator'),
            config('weather.temperature.thousands_separator'),
        ) . $unit;
    }
}
