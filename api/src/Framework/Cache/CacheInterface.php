<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Cache;

interface CacheInterface
{
    public function get(string $key): mixed;

    /** @param mixed $value A JSON-encodable cache value. */
    public function set(string $key, mixed $value, int $ttlSeconds): void;
}
