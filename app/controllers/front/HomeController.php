<?php

namespace App\controllers\front;

use App\core\Controller;
use App\models\Announcement;
use App\models\Application;
use App\models\Student;
use App\core\Security;

class HomeController extends Controller
{
    public function index()
    {
        $this->auth->requireAuth();
        $this->auth->requireStudent();

        $userId = $_SESSION['userId'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $studentModel = new Student();
        $studentData = $studentModel->getStudentByUserId((int)$userId);

        $announcementModel = new Announcement();
        $jobs = $announcementModel->allWithCompany(false);
        $totalJobs = $announcementModel->countActive();

        $applicationModel = new Application();
        $appliedJobIds = $studentData ? $applicationModel->jobIdsForStudent((int)$studentData['id']) : [];
        $applicationStatuses = $studentData ? $applicationModel->statusesForStudent((int)$studentData['id']) : [];

        return $this->render('frontend/index.twig', [
            'student' => $studentData,
            'jobs' => $jobs,
            'totalJobs' => $totalJobs,
            'appliedJobIds' => $appliedJobIds,
            'applicationStatuses' => $applicationStatuses
        ]);
    }

    public function jobs()
    {
        $this->auth->requireStudent();
        return $this->index();
    }


    public function login()
    {
        return $this->render('auth/login', []);
    }   
    public function loginSubmit()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if ($this->auth->login($email, $password)) {
            header('Location: /');
            exit;
        } else {
            $errors = $this->auth->errors();
            return $this->render('auth/login', ['errors' => $errors]);
        };
    }

    public function register()
    {
        return $this->render('/register.twig', [
            'errors' => [],
            'csrf_token' => Security::csrfToken()
        ]);
    }

    public function registerSubmit()
    {
        $token = (string)($_POST['csrf_token'] ?? '');
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->auth->register($name, $email, $password)) {
            header('Location: /login');
            exit;
        }

        return $this->render('/register.twig', [
            'errors' => $this->auth->errors(),
            'csrf_token' => Security::csrfToken()
        ]);
    }
}
