<?php

use App\Utils\ServiceContainer;

if (!function_exists('app')) {
    function app(string $class): mixed
    {
        return ServiceContainer::instance()->get($class);
    }
}

if (!function_exists('abort')) {
    function abort(string $message, int $code = 404): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode([
            'error' => $message
        ]);
        exit();
    }
}

if (!function_exists('dd')) {
    function dd(...$data): void
    {
        echo json_encode($data, JSON_PRETTY_PRINT,);
        exit();
    }
}

if (!function_exists('modelsToArray')) {
    function modelsToArray(array $models): array
    {
        return array_map(fn(object $instance) => $instance->toArray(), $models);
    }
}
