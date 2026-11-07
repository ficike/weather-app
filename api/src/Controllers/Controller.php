<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use DateTimeImmutable;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\RequestValidation\RequestValidationException;
use ADS\TakeHome\RequestValidation\RequestValidator;

/** Base controller shared by application controllers. */
abstract class Controller
{
    /**
     * @return array<string, string|list<string>|int|DateTimeImmutable>
     */
    protected function validateRequest(HttpRequest $request, RequestValidator $validator): array
    {
        $result = $validator->validate($request->query);
        if ($result['validated'] === null) {
            throw new RequestValidationException($result['errors']);
        }

        return $result['validated'];
    }
}
