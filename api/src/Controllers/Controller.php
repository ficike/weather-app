<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use ADS\TakeHome\Framework\Http\HttpRequest;
use ADS\TakeHome\RequestValidation\Core\RequestValidationException;
use ADS\TakeHome\RequestValidation\Core\RequestValidator;
use DateTimeImmutable;

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
