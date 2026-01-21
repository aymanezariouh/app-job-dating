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
$router->get('/admin/announcements', [App\controllers\back\AnnouncementController::class, 'index']);
$router->get('/admin/announcements/create', [App\controllers\back\AnnouncementController::class, 'create']);
$router->post('/admin/announcements', [App\controllers\back\AnnouncementController::class, 'store']);

$router->get('/admin/announcements/edit', [App\controllers\back\AnnouncementController::class, 'edit']);
$router->post('/admin/announcements/update', [App\controllers\back\AnnouncementController::class, 'update']);

$router->post('/admin/announcements/archive', [App\controllers\back\AnnouncementController::class, 'archive']);
$router->post('/admin/announcements/restore', [App\controllers\back\AnnouncementController::class, 'restore']);
$router->get('/admin/companies', [App\controllers\back\CompanyController::class, 'index']);
$router->get('/admin/companies/create', [App\controllers\back\CompanyController::class, 'create']);
$router->post('/admin/companies', [App\controllers\back\CompanyController::class, 'store']);

$router->get('/admin/companies/edit', [App\controllers\back\CompanyController::class, 'edit']);
$router->post('/admin/companies/update', [App\controllers\back\CompanyController::class, 'update']);

$router->post('/admin/companies/delete', [App\controllers\back\CompanyController::class, 'delete']);
$router->get('/admin/students', [App\controllers\back\StudentController::class, 'index']);

return $router;
