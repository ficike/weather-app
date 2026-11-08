<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Http;

use Throwable;

/** Coordinates request dispatch, exception mapping, and response emission. */
class HttpKernel
{
    private readonly HttpExceptionResponseFactory $exceptionResponseFactory;

    public function __construct(
        private readonly Router $router,
        private readonly HttpResponseEmitter $responseEmitter,
        bool $debug = false,
    ) {
        $this->exceptionResponseFactory = new HttpExceptionResponseFactory($debug);
    }

    public function handleRequest(): void
    {
        try {
            $response = $this->router->dispatch(HttpRequest::fromGlobals());
        } catch (Throwable $exception) {
            $response = $this->exceptionResponseFactory->create($exception);
        }

        $this->responseEmitter->emit($response);
    }
}
