<?php
class Router {
    private $routes = [];

    public function add(string $method, string $path, string $handlerFile, array $middleware = []) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handlerFile,
            'middleware' => $middleware
        ];
    }

    public function dispatch(string $requestUri, string $requestMethod) {
        $requestPath = parse_url($requestUri, PHP_URL_PATH);
        if ($requestPath !== '/') {
            $requestPath = rtrim($requestPath, '/');
        }

        foreach ($this->routes as $route) {
            if (strtoupper($requestMethod) !== $route['method']) continue;

            $pattern = preg_replace('#\{([\w-]+)\}#', '(?P<\1>[^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $requestPath, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) $_GET[$key] = $value;
                }

                // 🔐 Middleware prüfen
                foreach ($route['middleware'] as $m) {
                    if (str_starts_with($m, 'permission:')) {
                        $perm = explode(':', $m)[1];
                        Middleware::permission($perm);
                    } elseif (str_starts_with($m, 'role:')) {
                        $role = explode(':', $m)[1];
                        Middleware::role($role);
                    }
                }

                if (file_exists($route['handler'])) {
                    include $route['handler'];
                    return;
                } else {
                    http_response_code(500);
                    echo "Handler-Datei nicht gefunden";
                    return;
                }
            }
        }

        http_response_code(404);
        echo "404 - Route nicht gefunden";
    }
}
