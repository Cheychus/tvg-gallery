<?php

use controllers\SiteController;
use controllers\ApiController;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();
try {
    $router->map('GET', '/', [SiteController::class, 'home']);
    $router->map('GET', '/gallery', [SiteController::class, 'gallery']);
    $router->map( 'GET', '/api/images/[i:id]', [ApiController::class, 'getImages'] );
    $router->map( 'POST', '/api/image/[i:id]', [ApiController::class, 'uploadImage'] );
    $router->map('DELETE', '/api/image/[i:id]', [ApiController::class, 'deleteImage'] );
} catch (Exception $e) {
    echo $e->getMessage();
}

$match = $router->match();

if ($match) {
    [$controller, $method] = $match['target'];
    if (class_exists($controller) && method_exists($controller, $method)) {
        $params = $match['params'];
        $controllerInstance = new $controller();
        call_user_func([$controllerInstance, $method], $params);
    } else {
        http_response_code(500);
        echo "Controller oder Methode nicht gefunden.";
    }
} else {
    http_response_code(404);
    require '../src/views/404.php';
    exit();
}


