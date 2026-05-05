<?php

namespace App;

use Exception;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    private function set(string $key, callable $resolver, bool $singleton = false): void {
        $this->bindings[$key] = [
            'resolver' => $resolver,
            'singleton' => $singleton
        ];
    }

    // Para crear una instancia por cada solicitud
    public function scoped(string $key, callable $resolver): void {
        $this->set($key, $resolver, false);
    }

    // Para crear una única instancia (singleton)
    public function singleton(string $key, callable $resolver): void {
        $this->set($key, $resolver, true);
    }

    public function get(string $key) {
        // Si ya existe instancia singleton -> devolverla
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->bindings[$key])) {
            throw new Exception("No binding found for {$key}");
        }

        $binding = $this->bindings[$key];
        $object = $binding['resolver']($this);

        // Si es singleton -> guardar instancia
        if ($binding['singleton']) {
            $this->instances[$key] = $object;
        }

        return $object;
    }
}