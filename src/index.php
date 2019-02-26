<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Access-Control-Allow-Origin, Origin, Accept, Content-Type');
header('Content-Type: application/json');

require '../vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) {
        $r->addRoute('POST', '/users', ['CRUD\Controller\User', 'create']);
        $r->addRoute('PUT', '/users/{id:\d+}', ['CRUD\Controller\User', 'update']);
        $r->addRoute('GET', '/users[/{id:\d+}]', ['CRUD\Controller\User', 'get']);
        $r->addRoute('DELETE', '/users/{id:\d+}', ['CRUD\Controller\User', 'delete']);
    }
);

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $class = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];
        $vars['request_body'] = json_decode(file_get_contents("php://input"), false);

        $manager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
        $class = new $class($manager);
        echo json_encode($class->{$method}($vars));
        break;
}
