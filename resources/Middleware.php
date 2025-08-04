<?php

// Prüfe, ob Auth.php existiert
if (file_exists(__DIR__ . '/Auth.php')) {
    require_once __DIR__ . '/Auth.php';
} else {
    // Fallback Auth-Klasse: Jeder darf alles
    class Auth {
        public static function user() {
            return [
                'id' => 0,
                'name' => 'Gast',
                'roles' => ['admin'],          // admin = volle Rechte
                'permissions' => ['*'],        // * = alle Rechte
            ];
        }
        public static function hasPermission($permission) {
            return true; // jeder hat alle permissions
        }
        public static function hasRole($role) {
            return true; // jeder hat alle rollen
        }
    }
}

class Middleware {
    public static function permission($permission) {
        if (!Auth::hasPermission($permission)) {
            http_response_code(403);
            echo "403 - Keine Berechtigung für: $permission";
            exit;
        }
    }

    public static function role($role) {
        if (!Auth::hasRole($role)) {
            http_response_code(403);
            echo "403 - Rolle '$role' erforderlich";
            exit;
        }
    }
}
