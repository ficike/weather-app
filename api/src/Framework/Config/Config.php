<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Config;

use InvalidArgumentException;
use RuntimeException;

final class Config
{
    private static ?self $instance = null;

    /** @param array<string, mixed> $items */
    private function __construct(private readonly array $items)
    {
    }

    public static function load(string $directory): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $items = [];
        $files = glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php') ?: [];
        foreach ($files as $file) {
            $value = require $file;
            if (!is_array($value)) {
                throw new InvalidArgumentException("A(z) {$file} konfigurációs fájlnak tömböt kell visszaadnia.");
            }
            $items[basename($file, '.php')] = $value;
        }

        return self::$instance = new self($items);
    }

    public static function getInstance(): self
    {
        return self::$instance ?? throw new RuntimeException('A konfiguráció még nincs betöltve.');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->items;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function require(string $key): mixed
    {
        $value = $this->get($key);
        if ($value === null) {
            throw new InvalidArgumentException("A(z) {$key} konfigurációs érték kötelező.");
        }

        return $value;
    }
}
