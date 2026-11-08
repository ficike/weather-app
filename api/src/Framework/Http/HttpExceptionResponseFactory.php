<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Http;

use ADS\TakeHome\RequestValidation\Core\RequestValidationException;
use Throwable;

/** Maps exceptions to client-safe HTTP responses. */
final class HttpExceptionResponseFactory
{
    public function __construct(private readonly bool $debug = false)
    {
    }

    public function create(Throwable $exception): JsonHttpResponse
    {
        if ($exception instanceof RequestValidationException) {
            return new JsonHttpResponse(['errors' => $exception->errors], 400);
        }

        if (!$this->debug) {
            return new JsonHttpResponse(['error' => 'Szerveroldali hiba.'], 500);
        }

        return new JsonHttpResponse([
            'error' => $exception->getMessage(),
            'exception' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stackTrace' => $exception->getTraceAsString(),
        ], 500);
    }
}
