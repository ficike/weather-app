<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

use DateTimeImmutable;
use LogicException;

/** Runs a set of Laravel-style validation rules against request data. */
class RequestValidator
{
    /**
     * @param array<string, list<string>> $rules
     */
    public function __construct(private readonly array $rules)
    {
    }

    /**
     * @param array<string, string|list<string>> $data
     * @return array{validated: array|null, errors: array}
     */
    public function validate(array $data): array
    {
        $errors = [];
        $validated = [];

        foreach ($this->rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ($fieldRules as $rule) {
                [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null);
                $error = $this->validateRule($name, $argument, $value, $data);
                if ($error !== null) {
                    $errors[$field][] = $error;
                    // Match Laravel's bail behavior: stop after the first failing rule for a field.
                    break;
                }
            }

            if (!isset($errors[$field])) {
                $validated[$field] = $this->castValue($value, $fieldRules);
            }
        }

        if ($errors !== []) {
            return ['validated' => null, 'errors' => $errors];
        }

        return ['validated' => $validated, 'errors' => []];
    }

    /**
     * @param list<string> $rules
     * @return string|list<string>|int|DateTimeImmutable
     */
    private function castValue(string|array|null $value, array $rules): string|array|int|DateTimeImmutable
    {
        foreach ($rules as $rule) {
            [$name] = explode(':', $rule, 2);
            if ($name === 'integer' && is_string($value)) {
                return (int) $value;
            }

            if ($name === 'date_format' && is_string($value)) {
                [, $format] = explode(':', $rule, 2);
                $date = DateTimeImmutable::createFromFormat('!' . $format, $value);
                if ($date === false) {
                    throw new LogicException('A validált dátumot nem sikerült feldolgozni.');
                }
                return $date;
            }
        }

        return $value ?? '';
    }

    /**
     * @param array<string, string|list<string>> $data
     */
    private function validateRule(string $rule, ?string $argument, string|array|null $value, array $data): ?string
    {
        if ($rule === 'required') {
            return $value === null || $value === '' || $value === []
                ? 'A mező megadása kötelező.'
                : null;
        }

        if ($value === null || $value === '') {
            return null;
        }

        if ($rule === 'integer') {
            return is_string($value) && preg_match('/^-?\d+$/', $value)
                ? null
                : 'A mezőnek egész számnak kell lennie.';
        }

        if ($rule === 'min') {
            return is_string($value) && is_numeric($value) && (float) $value >= (float) $argument
                ? null
                : "A mező értéke legalább {$argument} kell legyen.";
        }

        if ($rule === 'date_format') {
            if (!is_string($value) || $argument !== 'Ymd' || !preg_match('/^\d{8}$/', $value)) {
                return 'A dátum formátuma YYYYMMDD legyen.';
            }

            return checkdate((int) substr($value, 4, 2), (int) substr($value, 6, 2), (int) substr($value, 0, 4))
                ? null
                : 'A mező nem érvényes dátum.';
        }

        if ($rule === 'after_or_equal') {
            $other = $data[$argument] ?? null;
            return is_string($value) && is_string($other) && $value >= $other
                ? null
                : "A dátumnak az {$argument} dátummal egyezőnek vagy annál későbbinek kell lennie.";
        }

        return null;
    }
}
