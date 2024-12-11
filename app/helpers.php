<?php

use App\Utils\Database\Setting;
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

if (!function_exists('find')) {
    function find(array $array, callable $callback): mixed
    {
        foreach ($array as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }
}

if (!function_exists('ticTacToeOpen')) {
    function ticTacToeOpen(): bool
    {
        $settings = (new Setting)->all();

        $fromTime = find($settings, fn($setting) => $setting->get('key') === 'tic-tac-toe-from')?->get('value') ?? null;
        $toTime = find($settings, fn($setting) => $setting->get('key') === 'tic-tac-toe-to')?->get('value') ?? null;

        if (!$fromTime || !$toTime) {
            return false;
        }

        $currentTime = date('H:i');

        if ($currentTime < $fromTime || $currentTime > $toTime) {
            return false;
        }

        return true;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }
}

if (!function_exists('url_is')) {
    function url_is(string $url = '/'): string
    {
        return $url === parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
