<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Http;

/** Sends an HTTP response to the PHP runtime. */
class HttpResponseEmitter
{
    public function emit(HttpResponseInterface $response): void
    {
        http_response_code($response->getStatus());

        foreach ($response->getHeaders() as $name => $value) {
            header("{$name}: {$value}");
        }

        $body = $response->getBody();
        if ($body !== null) {
            echo $body;
        }
    }
}
