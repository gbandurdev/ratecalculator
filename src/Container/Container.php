<?php

namespace App\Container;

use Closure;
use RuntimeException;

final class Container
{
    /**
     * @var array<string, Closure(Container): mixed>
     */
    private array $services = [];

    /**
     * Register a service factory.
     *
     * @param string $id
     * @param Closure(Container): mixed $factory
     */
    public function set(string $id, Closure $factory): void
    {
        $this->services[$id] = $factory;
    }

    /**
     * Resolve a service by ID.
     *
     * @template T
     * @param string $id
     * @return T
     */
    public function get(string $id): mixed
    {
        if (!isset($this->services[$id])) {
            throw new RuntimeException("Service '$id' not found.");
        }

        return ($this->services[$id])($this);
    }
}
