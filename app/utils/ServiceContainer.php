<?php

namespace App\Utils;

class ServiceContainer
{
    protected static ?self $instance = null;

    protected array $services = [];

    protected function __construct() {}

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function set(string $name, object $instance): self
    {
        $this->services[$name] = $instance;

        return $this;
    }

    public function get(string $name): ?object
    {
        if (!array_key_exists($name, $this->services)) {
            return null;
        }

        return $this->services[$name];
    }
}
