<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core\Rules;

use ADS\TakeHome\RequestValidation\Core\ValidationRuleInterface;

final class StringRule implements ValidationRuleInterface
{
    public function validate(mixed $value, ?string $argument, array $data): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_string($value) ? null : 'A mezőnek szövegnek kell lennie.';
    }

    public function cast(mixed $value, ?string $argument): mixed
    {
        return $value;
    }
}
