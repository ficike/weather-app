<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core\Rules;

use ADS\TakeHome\RequestValidation\Core\ValidationRuleInterface;

final class IntegerRule implements ValidationRuleInterface
{
    public function validate(mixed $value, ?string $argument, array $data): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_string($value) && preg_match('/^-?\d+$/', $value)
            ? null
            : 'A mezőnek egész számnak kell lennie.';
    }

    public function cast(mixed $value, ?string $argument): mixed
    {
        return is_string($value) ? (int) $value : $value;
    }
}
