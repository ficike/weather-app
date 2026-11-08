<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core\Rules;

use ADS\TakeHome\RequestValidation\Core\ValidationRuleInterface;

final class AfterOrEqualRule implements ValidationRuleInterface
{
    public function validate(mixed $value, ?string $argument, array $data): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $other = $data[$argument] ?? null;
        return is_string($value) && is_string($other) && $value >= $other
            ? null
            : "A dátumnak az {$argument} dátummal egyezőnek vagy annál későbbinek kell lennie.";
    }

    public function cast(mixed $value, ?string $argument): mixed
    {
        return $value;
    }
}
