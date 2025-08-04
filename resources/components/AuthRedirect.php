<?php

class AuthRedirect {
    public static function requireLogin($redirect = '/auth/login') {
        require_once __DIR__ . 'Auth.php';
        if (!Auth::user()) {
            header("Location: $redirect");
            exit;
        }
    }

    public static function onlyGuests($redirect = '/dashboard') {
        require_once __DIR__ . 'Auth.php';
        if (Auth::user()) {
            header("Location: $redirect");
            exit;
        }
    }
}
