<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Database;

/** Provides database queries as lists of associative rows. */
interface DatabaseConnectionInterface
{
    /**
     * @param array<string, scalar|null> $params Named query parameters.
     * @return list<array<string, scalar|null>>
     */
    public function queryAssoc(string $sql, array $params = []): array;
}
