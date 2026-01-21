<?php

namespace App\core;

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function matchRoute($route, $path)
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches);
            preg_match_all('/\{([^}]+)\}/', $route, $paramNames);
            return $params = array_combine($paramNames[1], $matches);
        }
        return false;
    }

    public function resolve()
{
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    $path = parse_url($path, PHP_URL_PATH);
    $path = strtolower($path);


    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
        if ($path === '') $path = '/';
    }

    if ($path !== '/') {
        $path = rtrim($path, '/');
        if ($path === '') $path = '/';
    }

    $callback = $this->routes[$method][$path] ?? false;

    if ($callback) {
        if (is_callable($callback)) {
            return call_user_func($callback);
        }
        if (is_array($callback) && is_string($callback[0])) {
            $callback[0] = new $callback[0]();
            return call_user_func($callback);
        }
        return $callback;
    }

    foreach ($this->routes[$method] ?? [] as $route => $callback) {
        $params = $this->matchRoute($route, $path);
        if ($params !== false) {
            if (is_callable($callback)) {
                return call_user_func($callback, $params);
            }
            if (is_array($callback) && is_string($callback[0])) {
                $callback[0] = new $callback[0]();
                return call_user_func($callback, $params);
            }
            return $callback;
        }
    }

    http_response_code(404);
    return "404 Not Found";
}

}
