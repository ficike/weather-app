<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core\Rules;

use ADS\TakeHome\RequestValidation\Core\ValidationRuleInterface;

final class RequiredRule implements ValidationRuleInterface
{
    public function validate(mixed $value, ?string $argument, array $data): ?string
    {
        return $value === null || $value === '' || $value === []
            ? 'A mező megadása kötelező.'
            : null;
    }

    public function cast(mixed $value, ?string $argument): mixed
    {
        return $value;
    }
}
