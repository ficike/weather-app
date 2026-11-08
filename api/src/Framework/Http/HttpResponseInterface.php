<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Http;

/**
 * HTTP válasz.
 *
 * Minden konkrét HTTP válasz típusnak ezt az interface-t kell megvalósítania.
 */
interface HttpResponseInterface
{
    /**
     * HTTP státusz kód (100 - 599).
     */
    public function getStatus(): int;

    /**
     * HTTP válasz tartalma.
     */
    public function getBody(): string|null;

    /** @return array<string, string> HTTP headers to send with the response. */
    public function getHeaders(): array;
}
