<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Http;

/**
 * HTTP kérés.
 *
 * A keretrendszer egy ilyen objektumot ad át a végpont kezelő függvényeknek.
 */
class HttpRequest
{
    /**
     * @param array<string, string|list<string>> $query A kérés query paraméterei.
     */
    public function __construct(
        public readonly array $query,
        public readonly string $method = 'GET',
        public readonly string $path = '/',
    ) {
    }

    /**
     * Létrehoz egy új kérést globális PHP változókból.
     */
    public static function fromGlobals(): HttpRequest
    {
        return new HttpRequest(
            query: $_GET,
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            path: self::requestPath($_SERVER['REQUEST_URI'] ?? '/'),
        );
    }

    private static function requestPath(string $requestUri): string
    {
        $path = parse_url($requestUri, PHP_URL_PATH);
        return rawurldecode(is_string($path) ? $path : '/');
    }
}
