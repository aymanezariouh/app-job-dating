<?php

use App\core\Router;
use App\controllers\back\AuthController;
use App\controllers\front\HomeController;
use App\controllers\back\DashboardController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/about', function () { return "About page"; });

$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginSubmit']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/admin/dashboard', [DashboardController::class, 'index']);
$router->get('/jobs', [HomeController::class, 'jobs']);

return $router;
