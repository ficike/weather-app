<?php

declare(strict_types=1);

namespace ADS\TakeHome\Services\Weather;

/** Calculates median and mode for the temperature columns in weather rows. */
class WeatherStatisticsCalculator
{
    /**
     * @param list<array<string, int|float|string|null>> $rows
     * @return array<string, array{median: float|null, mode: list<float>}>
     */
    public function calculate(array $rows): array
    {
        $summary = [];
        foreach (['min_temp', 'max_temp'] as $column) {
            $values = $this->extractSortedValues($rows, $column);
            $summary[$column] = [
                'median' => $this->median($values),
                'mode' => $this->modes($values),
            ];
        }

        return $summary;
    }

    /**
     * @param list<array<string, int|float|string|null>> $rows
     * @return list<float>
     */
    private function extractSortedValues(array $rows, string $column): array
    {
        $values = [];
        foreach ($rows as $row) {
            if ($row[$column] !== null) {
                $values[] = (float) $row[$column];
            }
        }

        sort($values, SORT_NUMERIC);
        return $values;
    }

    /** @param list<float> $values */
    private function median(array $values): ?float
    {
        if ($values === []) {
            return null;
        }

        $middle = intdiv(count($values), 2);
        if (count($values) % 2 === 1) {
            return $values[$middle];
        }

        return ($values[$middle - 1] + $values[$middle]) / 2;
    }

    /** @param list<float> $values
     *  @return list<float>
     */
    private function modes(array $values): array
    {
        if ($values === []) {
            return [];
        }

        $frequencies = array_count_values(array_map(static fn (float $value): string => (string) $value, $values));
        $maxFrequency = max($frequencies);
        if ($maxFrequency === 1) {
            return [];
        }

        $modes = [];
        foreach ($frequencies as $value => $frequency) {
            if ($frequency === $maxFrequency) {
                $modes[] = (float) $value;
            }
        }

        return $modes;
    }
}
