<?php

use App\core\Router;
use App\controllers\front\HomeController;
$router = new Router();

// Define your routes here
$router->get('/home', [HomeController::class, 'index']);


$router->get('/user/{id}', function($params) {
    return "User ID: " . $params['id'];
});


$router->get('/login', [HomeController::class, 'login']);
$router->post('/login', [HomeController::class, 'loginSubmit']);

$router->get('/register', [HomeController::class, 'register']);
$router->post('/register', [HomeController::class, 'registerSubmit']);

return $router;