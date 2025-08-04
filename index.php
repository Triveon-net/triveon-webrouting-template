<?php

$router = require 'resources/routes.php';

$method = $_SERVER['REQUEST_METHOD'];

// Unterstützung für PUT/DELETE über X-HTTP-Method-Override
if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

$router->dispatch($_SERVER['REQUEST_URI'], $method);
