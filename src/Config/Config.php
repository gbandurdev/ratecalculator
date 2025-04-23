<?php

declare(strict_types=1);

namespace App\Config;

readonly class Config
{
    public function __construct(
        private array $config,
    ) {}

    /**
     * Get a config value by key with optional default.
     *
     * @template T
     * @param string $key
     * @param T|null $default
     * @return T|null
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}