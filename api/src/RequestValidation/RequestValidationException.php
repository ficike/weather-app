<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation;

use RuntimeException;

class RequestValidationException extends RuntimeException
{
    /** @param array<string, list<string>> $errors */
    public function __construct(public readonly array $errors)
    {
        parent::__construct('A kérés validációja sikertelen.');
    }
}
