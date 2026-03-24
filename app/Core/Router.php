<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $pattern, array|callable $handler, array $middlewares = []): void
    {
        $this->add('GET', $pattern, $handler, $middlewares);
    }

    public function post(string $pattern, array|callable $handler, array $middlewares = []): void
    {
        $this->add('POST', $pattern, $handler, $middlewares);
    }

    public function any(string $pattern, array|callable $handler, array $middlewares = []): void
    {
        $this->add('GET',  $pattern, $handler, $middlewares);
        $this->add('POST', $pattern, $handler, $middlewares);
    }

    public function group(string $prefix, callable $callback, array $middlewares = []): void
    {
        $previousMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($this->middlewares, $middlewares);

        // Gecici router ile grup rotalarini topla
        $groupRouter = new self();
        $callback($groupRouter);

        foreach ($groupRouter->routes as $route) {
            $this->add(
                $route['method'],
                $prefix . $route['pattern'],
                $route['handler'],
                array_merge($this->middlewares, $route['middlewares'])
            );
        }

        $this->middlewares = $previousMiddlewares;
    }

    private function add(string $method, string $pattern, array|callable $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method'      => $method,
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // POST override (_method field ile PUT, DELETE destegi)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Base path siyir
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($scriptDir && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = '/' . trim($uri, '/');
        if ($uri === '') $uri = '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $regex = $this->toRegex($route['pattern']);

            if (!preg_match($regex, $uri, $matches)) continue;

            // Adlandirilmis parametreler
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // Middleware calistir
            foreach ($route['middlewares'] as $middlewareClass) {
                $middleware = new $middlewareClass();
                $middleware->handle();
            }

            $this->call($route['handler'], $params);
            return;
        }

        // 404
        $this->notFound();
    }

    private function toRegex(string $pattern): string
    {
        // :slug -> (?P<slug>[^/]+)
        // :id   -> (?P<id>[0-9]+)
        $regex = preg_replace('/:([a-z_]+)/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    private function call(array|callable $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        [$class, $method] = $handler;

        // Controller sinifini yukle
        if (!str_contains($class, '\\')) {
            $class = 'App\\Controllers\\' . $class;
        }

        if (!class_exists($class)) {
            Logger::error("Controller bulunamadi: {$class}");
            $this->notFound();
            return;
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            Logger::error("Method bulunamadi: {$class}::{$method}");
            $this->notFound();
            return;
        }

        $controller->$method($params);
    }

    private function notFound(): void
    {
        http_response_code(404);
        $viewPath = APP_PATH . '/Views/Store/errors/404.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '<h1>404 - Sayfa Bulunamadi</h1>';
        }
    }
}
