<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core;

use DateTimeImmutable;

interface ValidationRuleInterface
{
    /**
     * @param string|list<string>|int|DateTimeImmutable|null $value
     * @param array<string, string|list<string>> $data
     */
    public function validate(mixed $value, ?string $argument, array $data): ?string;

    /** @param string|list<string>|int|DateTimeImmutable|null $value */
    public function cast(mixed $value, ?string $argument): mixed;
}
