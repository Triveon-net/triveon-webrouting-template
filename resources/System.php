<?php
// resources/components/System.php

class System {
    private static string $basePath = __DIR__; // → /resources/components

    public static function require(string $path) {
        $real = self::resolve($path);
        if (!file_exists($real)) {
            throw new Exception("System-Komponente nicht gefunden: $real");
        }
        require_once $real;
    }

    public static function include(string $path) {
        $real = self::resolve($path);
        if (!file_exists($real)) {
            throw new Exception("System-Komponente nicht gefunden: $real");
        }
        include $real;
    }

    public static function resolve(string $path): string {
        $normalized = ltrim($path, '/'); // z. B. '/AuthRedirect' → 'AuthRedirect'
        return self::$basePath . '/' . $normalized . '.php';
    }
}
