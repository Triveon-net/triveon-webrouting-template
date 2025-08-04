<?php

require_once 'Router.php';
require_once 'Middleware.php';

$router = new Router();

// Diese Route erfordert die Permission "edit_post"
$router->add('GET', '/', 'pages/index.php', ['']);
$router->add('GET', '/directory2', 'pages/directory2/index.php', ['']);
// Diese Route erfordert die Rolle "admin"
$router->add('DELETE', '/user/{id}', 'pages/user/delete.php', ['role:admin']);

return $router;
