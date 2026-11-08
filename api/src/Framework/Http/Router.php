<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Http;

use Closure;

/**
 * Egyszerű HTTP router.
 */
class Router
{
    /**
     * @var array<string, array<string, Closure(HttpRequest):HttpResponseInterface>>
     */
    private array $routeGroups = [];

    /**
     * Regisztrál egy új végpontot.
     *
     * Az egyszerűség kedvéért most path paramétereket nem kezelünk, csak
     * pontos egyezőséget figyelünk.
     *
     * @param string $method HTTP metódus, pl. "GET", "POST"
     * @param string $path A végpont elérési útja, az URL path része, pl. "/hello"
     * @param Closure(HttpRequest):HttpResponseInterface $handler
     */
    public function addRoute(string $method, string $path, Closure $handler): void
    {
        $routeGroup = $this->routeGroups[$path] ?? [];
        $routeGroup[$method] = $handler;
        $this->routeGroups[$path] = $routeGroup;
    }

    /** Resolves a request to its route response. */
    public function dispatch(HttpRequest $request): HttpResponseInterface
    {
        $routeGroup = $this->routeGroups[$request->path] ?? null;
        if ($routeGroup === null) {
            return new JsonHttpResponse(['error' => 'nem található'], 404);
        }

        $handler = $routeGroup[$request->method] ?? null;
        if ($handler === null) {
            return new JsonHttpResponse(["error" => "{$request->method} nem engedélyezett"], 405);
        }

        return $handler($request);
    }
}
