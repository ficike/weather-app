<?php

declare(strict_types=1);

use ADS\TakeHome\Framework\Config\Config;

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::getInstance()->get($key, $default);
    }
}
