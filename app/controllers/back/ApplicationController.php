<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;
use App\models\Application;

class ApplicationController extends Controller
{
    private Application $applicationModel;

    public function __construct()
    {
        parent::__construct();
        $this->applicationModel = new Application();
    }

    public function index()
    {
        $this->auth->requireAdmin();

        $applications = $this->applicationModel->allWithDetails();
        $flash = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        return $this->render('back/applications/index.twig', [
            'applications' => $applications,
            'flash_success' => $flash,
            'csrf_token' => Security::csrfToken()
        ]);
    }

    public function approve()
    {
        $this->auth->requireAdmin();

        $token = (string)($_POST['csrf_token'] ?? '');
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->applicationModel->updateStatus($id, 'accepted');
            $_SESSION['flash_success'] = "Application accepted.";
        }

        header('Location: /admin/applications');
        exit;
    }

    public function deny()
    {
        $this->auth->requireAdmin();

        $token = (string)($_POST['csrf_token'] ?? '');
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->applicationModel->updateStatus($id, 'rejected');
            $_SESSION['flash_success'] = "Application rejected.";
        }

        header('Location: /admin/applications');
        exit;
    }
}
