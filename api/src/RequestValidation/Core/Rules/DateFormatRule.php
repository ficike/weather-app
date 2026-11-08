<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core\Rules;

use ADS\TakeHome\RequestValidation\Core\ValidationRuleInterface;
use DateTimeImmutable;
use LogicException;

final class DateFormatRule implements ValidationRuleInterface
{
    public function validate(mixed $value, ?string $argument, array $data): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value) || $argument !== 'Ymd' || !preg_match('/^\d{8}$/', $value)) {
            return 'A dátum formátuma YYYYMMDD legyen.';
        }

        return checkdate((int) substr($value, 4, 2), (int) substr($value, 6, 2), (int) substr($value, 0, 4))
            ? null
            : 'A mező nem érvényes dátum.';
    }

    public function cast(mixed $value, ?string $argument): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $date = DateTimeImmutable::createFromFormat('!' . $argument, $value);
        if ($date === false) {
            throw new LogicException('A validált dátumot nem sikerült feldolgozni.');
        }

        return $date;
    }
}
