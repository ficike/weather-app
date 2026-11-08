<?php

declare(strict_types=1);

namespace ADS\TakeHome\DTO;

use DateTimeImmutable;
use JsonSerializable;
use LogicException;
use UnexpectedValueException;

class WeatherData implements JsonSerializable
{
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly ?float $minTemp,
        public readonly ?float $maxTemp,
        public readonly ?float $precipitation,
    ) {
    }

    /** @param array<string, int|float|string|null> $row */
    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            self::parseDate(self::requiredField($row, 'date')),
            self::nullableFloat(self::requiredField($row, 'min_temp'), 'min_temp'),
            self::nullableFloat(self::requiredField($row, 'max_temp'), 'max_temp'),
            self::nullableFloat(self::requiredField($row, 'precipitation'), 'precipitation'),
        );
    }

    /** @return array{date: string, min_temp: float|null, max_temp: float|null, precipitation: float|null} */
    public function jsonSerialize(): array
    {
        $outputFormat = config('date.output_format');
        if (!is_string($outputFormat)) {
            throw new LogicException('A dátum kimeneti formátuma nincs megfelelően konfigurálva.');
        }

        return [
            'date' => $this->date->format($outputFormat),
            'min_temp' => $this->minTemp,
            'max_temp' => $this->maxTemp,
            'precipitation' => $this->precipitation,
        ];
    }

    private static function requiredField(array $row, string $field): mixed
    {
        if (!array_key_exists($field, $row)) {
            throw new UnexpectedValueException("Az adatbázisból érkező {$field} mező hiányzik.");
        }

        return $row[$field];
    }

    private static function parseDate(mixed $value): DateTimeImmutable
    {
        if (!is_int($value) && !is_float($value) && !is_string($value)) {
            throw new UnexpectedValueException('Az adatbázisból érkező dátum érvénytelen.');
        }

        $inputFormat = config('date.input_format');
        if (!is_string($inputFormat)) {
            throw new LogicException('A dátum bemeneti formátuma nincs megfelelően konfigurálva.');
        }

        $date = DateTimeImmutable::createFromFormat($inputFormat, (string) $value);
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new UnexpectedValueException('Az adatbázisból érkező dátum formátuma érvénytelen.');
        }

        return $date;
    }

    private static function nullableFloat(mixed $value, string $field): ?float
    {
        if ($value === null) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new UnexpectedValueException("Az adatbázisból érkező {$field} érték nem numerikus.");
        }

        return (float) $value;
    }
}
