<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework\Cache;

use JsonException;
use RuntimeException;

final class FileCache implements CacheInterface
{
    public function __construct(private readonly string $directory)
    {
    }

    public function get(string $key): mixed
    {
        $path = $this->pathFor($key);
        if (!is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            return null;
        }

        try {
            $entry = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            @unlink($path);
            return null;
        }

        if (!is_array($entry) || !isset($entry['expires_at']) || !is_int($entry['expires_at']) || !array_key_exists('value', $entry)) {
            @unlink($path);
            return null;
        }

        if ($entry['expires_at'] <= time()) {
            @unlink($path);
            return null;
        }

        return $entry['value'];
    }

    public function set(string $key, mixed $value, int $ttlSeconds): void
    {
        if ($ttlSeconds <= 0) {
            return;
        }

        if (!is_dir($this->directory) && !mkdir($this->directory, 0770, true) && !is_dir($this->directory)) {
            throw new RuntimeException("A cache könyvtár nem hozható létre: {$this->directory}");
        }

        if (random_int(1, 100) === 1) {
            $this->cleanupExpiredEntries();
        }

        try {
            $contents = json_encode([
                'expires_at' => time() + $ttlSeconds,
                'value' => $value,
            ], JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('A cache érték JSON formátumba nem alakítható.', previous: $exception);
        }
        if (!is_string($contents)) {
            throw new RuntimeException('A cache érték JSON formátumba nem alakítható.');
        }

        $temporaryPath = tempnam($this->directory, 'cache_');
        if ($temporaryPath === false) {
            throw new RuntimeException("Ideiglenes cache fájl nem hozható létre: {$this->directory}");
        }

        try {
            if (file_put_contents($temporaryPath, $contents, LOCK_EX) === false) {
                throw new RuntimeException("A cache fájl nem írható: {$temporaryPath}");
            }

            if (!rename($temporaryPath, $this->pathFor($key))) {
                throw new RuntimeException("A cache fájl nem helyezhető át: {$temporaryPath}");
            }
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }

    private function pathFor(string $key): string
    {
        return rtrim($this->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
    }

    private function cleanupExpiredEntries(): void
    {
        foreach (glob(rtrim($this->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.json') ?: [] as $path) {
            $contents = file_get_contents($path);
            if ($contents === false) {
                continue;
            }

            try {
                $entry = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                @unlink($path);
                continue;
            }

            if (!is_array($entry) || !isset($entry['expires_at']) || !is_int($entry['expires_at']) || $entry['expires_at'] <= time()) {
                @unlink($path);
            }
        }
    }
}
