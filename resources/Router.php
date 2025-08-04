<?php
class Router {
    private $routes = [];

    public function add(string $method, string $url, string $pathAlias, array $middleware = []) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'url' => $url,
            'path' => $pathAlias,
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

            $pattern = preg_replace('#\{([\w-]+)\}#', '(?P<\1>[^/]+)', $route['url']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $requestPath, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) $_GET[$key] = $value;
                }

                // 💡 Middleware prüfen
                foreach ($route['middleware'] as $m) {
                    if (str_starts_with($m, 'permission:')) {
                        Middleware::permission(substr($m, 11));
                    } elseif (str_starts_with($m, 'role:')) {
                        Middleware::role(substr($m, 5));
                    } elseif ($m === 'auth') {
                        Middleware::auth();
                    }
                }

                // 🔄 Alias zu Dateipfad umwandeln
                $handlerFile = $this->resolvePath($route['path']);
                if (file_exists($handlerFile)) {
                    include $handlerFile;
                    return;
                }

                http_response_code(500);
                echo "Handler-Datei nicht gefunden: $handlerFile";
                return;
            }
        }

        http_response_code(404);
        echo "404 - Route nicht gefunden";
    }

    private function resolvePath(string $alias): string {
        $alias = str_replace('.', '/', $alias); // z. B. 'admin.dashboard' → 'admin/dashboard'
        return __DIR__ . "/pages/{$alias}.php"; // absoluten Pfad erzeugen
    }
}
