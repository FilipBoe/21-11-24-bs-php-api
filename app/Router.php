<?php

namespace App;

class Router
{
    protected array $routes = [];

    protected array $middlewares = [];

    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }


    public function get(string $path, callable $handler, array $middleware = [])
    {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function post(string $path, callable $handler, array $middleware = [])
    {
        $this->routes[] = [
            'method' => 'POST',
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function delete(string $path, callable $handler, array $middleware = [])
    {
        $this->routes[] = [
            'method' => 'DELETE',
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function match(string $method, string $path)
    {
        if (preg_match('/\.css$/', $_SERVER['REQUEST_URI'])) {
            $cssPath = ltrim($_SERVER['REQUEST_URI'], '/');

            if (file_exists($cssPath)) {
                header('Content-Type: text/css');
                readfile($cssPath);
                exit;
            }
        }

        foreach ($this->routes as $route) {
            $routePath = preg_replace('/\{[^\}]+\}/', '([^/]+)', $route['path']);
            if ($route['method'] === $method && preg_match('#^' . $routePath . '$#', $path, $matches)) {
                array_shift($matches);

                $request = [
                    'headers' => getallheaders(),
                    'data' => json_decode(file_get_contents('php://input') ?? '', true) ?? [],
                ];

                $this->handleMiddlewares(
                    $route['middleware'],
                    $request
                );

                return $route['handler']($request, ...$matches);
            }
        }

        abort('Not found', 404);
    }

    protected function handleMiddlewares(array $requestedMiddlewares, array &$request): void
    {
        foreach ($requestedMiddlewares as $requestedMiddleware) {
            $request = array_merge_recursive($request, $this->middlewares[$requestedMiddleware]($request));
        }
    }

    public static function jsonResponse(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);

        echo json_encode($data);

        exit();
    }

    public static function view(string $view, array $data = [], string $layout = 'layout'): void
    {
        $layoutFile = __DIR__ . "/views/{$layout}.php";

        if (!file_exists($layoutFile)) {
            abort('Layout not found', 500);
        }

        $viewFile = __DIR__ . "/views/{$view}.php";

        if (!file_exists($viewFile)) {
            abort('View not found', 500);
        }

        extract($data);

        ob_start();

        require $viewFile;

        // used in layout file
        $content = ob_get_clean();

        require $layoutFile;

        exit();
    }
}
